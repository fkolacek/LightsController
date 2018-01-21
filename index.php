<?php

    require "./inc/Loader.php";
    Loader::init();

    $API = new API($_GET);
    $API->addController(new ESPController("esp-ledstrip.example.com", "Kitchen", "LED strip in kitchen"));
    $API->addController(new HUEController("bridge.example.com/api/xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx/lights/4", ControllerType::HUE_LED_STRIP, "TV", "LED strip behind TV"));
    $API->addController(new ESPController("esp-ledstrip3.example.com", "Bed", "LED strip under bed"));
    $API->addController(new HUEController("bridge.example.com/api/xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx/lights/3", ControllerType::HUE_WHITE, "Lamp", "Lamp in living room"));

    if($API->isArg("api")){
        header('Content-type: application/json');
        die($API->processCall());
    }

    $controllers = $API->getControllers();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
  <title>Lights controllerr</title>
  <meta http-equiv="content-type" content="text/html;charset=utf-8" />
  <link rel="stylesheet" href="css/bootstrap.min.css" type="text/css" />
  <link rel="stylesheet" href="css/bootstrap-switch.min.css" type="text/css" />
  <link rel="stylesheet" href="css/farbtastic.css" type="text/css" />
  <style type="text/css">
    #wrapper{ margin: 10px; background-color: #FFFFFF; display: inline-block; border: 1px solid #A2A2A2; }
    #control{ float: left; width: 350px; padding: 16px 16px 17px 16px;  }
    #colorpicker{ float: left; }
  </style>
</head>
<body>
  <div id="wrapper">
	<div id="control">
    <form method="GET" action="">
	  <div class="form-group row">
        <label for="controller" class="col-sm-3 col-form-label">Controller: </label>
        <div class="col-sm">
          <select id="controller" name="controller" class="form-control">
            <option value="" selected disabled>Please select</option>
            <?php
            foreach($controllers as $key=>$controller){
              echo "<option value=\"".$key."\">".$controller->getLabel()."</option>";
            }
            ?>
          </select>
        </div>
      </div>

	  <div class="form-group row">
        <label for="power" class="col-sm-3 col-form-label">Power: </label>
        <div class="col-sm">
          <input id="power" name="power" type="checkbox" data-toggle="toggle" />
        </div>
      </div>

	  <div class="form-group row">
        <label for="color" class="col-sm-3 col-form-label">Color: </label>
        <div class="col-sm">
          <input id="color" name="color" type="text"  value="#000000" class="form-control" />
        </div>
      </div>

    </form>
    </div>
    <div id="colorpicker"></div>
    <div style="clear: both;"></div>
  </div>
  <script type="text/javascript" src="js/jquery.js"></script>
  <script type="text/javascript" src="js/bootstrap.min.js"></script>
  <script type="text/javascript" src="js/bootstrap-switch.min.js"></script>
  <script type="text/javascript" src="js/farbtastic.js"></script>
  <script type="text/javascript">
  $(document).ready(function(){
      function dec2hex(number){ return ("0"+number.toString(16)).slice(-2).toLowerCase(); }
      function hex2dec(number){ return parseInt(number, 16); }

      var ACTIVE = true;

	  $("[name='power']").bootstrapSwitch();

	  $("[name='controller']").on("change", function(){
	      var controller = $(this).val();

	      //Reset to defaults
          ACTIVE = false;
          $("[name='power']").bootstrapSwitch("state", false);
          $("[name='color']").val("#000000");
          $.farbtastic("#colorpicker").setColor("#000000");
          ACTIVE = true;

          $.getJSON("index.php", { api: 1, action: "get", controller: controller}, function(data){
              if(data['status'] == "OK"){
                  var state = parseInt(data['response']['state']);
                  var r = parseInt(data['response']['r']);
                  var g = parseInt(data['response']['g']);
                  var b = parseInt(data['response']['b']);
                  var hex = "#"+dec2hex(r)+dec2hex(g)+dec2hex(b);

                  ACTIVE = false;
                  $("[name='power']").bootstrapSwitch("state", ((state == 1)? true : false));
                  $("[name='color']").val(hex);
                  $.farbtastic("#colorpicker").setColor(hex);
                  ACTIVE = true;
              }
          });
	  });

	  $("#colorpicker").farbtastic(function(color){
	      if(!ACTIVE) return;

	      var controller = $("#controller").val();
	      var parts = color.match(/^\#([A-Fa-f0-9]{2})([A-Fa-f0-9]{2})([A-Fa-f0-9]{2})$/);
	      var r = hex2dec(parts[1]);
	      var g = hex2dec(parts[2]);
	      var b = hex2dec(parts[3]);

	      $.getJSON("index.php", { api: 1, action: "set", controller: controller, r: r, g: g, b: b });

	      $("[name='power']").bootstrapSwitch("state", true);
	      $("#color").val(color);
	  });

	  $(".bootstrap-switch-container span").on("click", function(){
	      var controller = $("#controller").val();

	      if($("[name='power']").is(":checked"))
	          $.getJSON("index.php", { api: 1, action: "on", controller: controller });
	      else
	          $.getJSON("index.php", { api: 1, action: "off", controller: controller });
	  });

	  $("#color").on("keyup", function(){
	      var color = $(this).val();
	      if(color.match(/^\#[A-Fa-f0-9]{6}$/))
	          $.farbtastic("#colorpicker").setColor(color);
	  });
  });
  </script>
</body>
</html>
