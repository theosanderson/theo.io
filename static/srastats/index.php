<title>SRA Stats</title>
  <script src="https://d3js.org/d3.v4.min.js"></script>
<script src='//splasho.com/petitions/mg/metricsgraphics.js'></script>
 <link href='//splasho.com/petitions/mg/metricsgraphics.css' rel='stylesheet' type='text/css'>
<meta name="twitter:card" content="summary" />
<meta name="twitter:creator" content="@theosanderson" />
<meta property="og:title" content="SRA stats" />
<meta property="og:description" content="How many petabases have we reached today?" />
<meta property="og:image" content="//splasho.com/petitions/graph.png" />

<?

$contents = file_get_contents("https://www.ncbi.nlm.nih.gov/Traces/sra/sra_stat.cgi");
$array = explode("\n", $contents);
$dates = array();
$bases = array();
$output= array();
$changes=array();

array_shift($array);
array_pop($array);





foreach( $array as $line){
	$elements = explode(",", $line);
	$dates[] = $elements[0];
	$bases[] = $elements[1];
	$output[]="{date:new Date(".$elements[0]."), n :".$elements[1]."}";
  if (count($bases)>count($array)-20){
    $changes[] = $bases[count($bases)-1] - $bases[count($bases)-2];
  }
}

$change_per_day = array_sum($changes)/count($changes);
$start = $bases[count($bases)-1];

$thing=implode(",",$output);
?>


<script>
	
data = [<?= $thing ?>];

function show_main(){
MG.data_graphic({
	top:100,
  left: 50,
  data: data,
  width: 700,
  height: 500,
  target: ".result",
  x_accessor: "date",
  y_accessor: "n",
  x_label:"Date",
  y_label:"Bases",
  color: ['tomato', '#CCFFFF']
	//mouseover: function(d, i) { console.log(d,i)	    }
});
};


</script>


<link href="https://fonts.googleapis.com/css?family=Arimo&display=swap" rel="stylesheet">
<script src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/3053004/odometer.min.js"></script>

<h1 style="margin-bottom:30px;">Number of bases in the Sequence Read Archive</h1>
<span class="odometer" id="element" >123456</span> <span style="font-size:120%">&nbsp;base pairs</span>

<style>
  *{font-family:Arimo}
#element{font-size:200%}
body{text-align:center}


.odometer.odometer-auto-theme, .odometer.odometer-theme-car {
  display: inline-block;
  vertical-align: middle;
  *vertical-align: auto;
  *zoom: 1;
  *display: inline;
  position: relative;
}
.odometer.odometer-auto-theme .odometer-digit, .odometer.odometer-theme-car .odometer-digit {
  display: inline-block;
  vertical-align: middle;
  *vertical-align: auto;
  *zoom: 1;
  *display: inline;
  position: relative;
}
.odometer.odometer-auto-theme .odometer-digit .odometer-digit-spacer, .odometer.odometer-theme-car .odometer-digit .odometer-digit-spacer {
  display: inline-block;
  vertical-align: middle;
  *vertical-align: auto;
  *zoom: 1;
  *display: inline;
  visibility: hidden;
}
.odometer.odometer-auto-theme .odometer-digit .odometer-digit-inner, .odometer.odometer-theme-car .odometer-digit .odometer-digit-inner {
  text-align: left;
  display: block;
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  overflow: hidden;
}
.odometer.odometer-auto-theme .odometer-digit .odometer-ribbon, .odometer.odometer-theme-car .odometer-digit .odometer-ribbon {
  display: block;
}
.odometer.odometer-auto-theme .odometer-digit .odometer-ribbon-inner, .odometer.odometer-theme-car .odometer-digit .odometer-ribbon-inner {
  display: block;
  -webkit-backface-visibility: hidden;
}
.odometer.odometer-auto-theme .odometer-digit .odometer-value, .odometer.odometer-theme-car .odometer-digit .odometer-value {
  display: block;
  -webkit-transform: translateZ(0);
}
.odometer.odometer-auto-theme .odometer-digit .odometer-value.odometer-last-value, .odometer.odometer-theme-car .odometer-digit .odometer-value.odometer-last-value {
  position: absolute;
}
.odometer.odometer-auto-theme.odometer-animating-up .odometer-ribbon-inner, .odometer.odometer-theme-car.odometer-animating-up .odometer-ribbon-inner {
  -webkit-transition: -webkit-transform 2s;
  -moz-transition: -moz-transform 2s;
  -ms-transition: -ms-transform 2s;
  -o-transition: -o-transform 2s;
  transition: transform 2s;
}
.odometer.odometer-auto-theme.odometer-animating-up.odometer-animating .odometer-ribbon-inner, .odometer.odometer-theme-car.odometer-animating-up.odometer-animating .odometer-ribbon-inner {
  -webkit-transform: translateY(-100%);
  -moz-transform: translateY(-100%);
  -ms-transform: translateY(-100%);
  -o-transform: translateY(-100%);
  transform: translateY(-100%);
}
.odometer.odometer-auto-theme.odometer-animating-down .odometer-ribbon-inner, .odometer.odometer-theme-car.odometer-animating-down .odometer-ribbon-inner {
  -webkit-transform: translateY(-100%);
  -moz-transform: translateY(-100%);
  -ms-transform: translateY(-100%);
  -o-transform: translateY(-100%);
  transform: translateY(-100%);
}
.odometer.odometer-auto-theme.odometer-animating-down.odometer-animating .odometer-ribbon-inner, .odometer.odometer-theme-car.odometer-animating-down.odometer-animating .odometer-ribbon-inner {
  -webkit-transition: -webkit-transform 2s;
  -moz-transition: -moz-transform 2s;
  -ms-transition: -ms-transform 2s;
  -o-transition: -o-transform 2s;
  transition: transform 2s;
  -webkit-transform: translateY(0);
  -moz-transform: translateY(0);
  -ms-transform: translateY(0);
  -o-transform: translateY(0);
  transform: translateY(0);
}

.odometer.odometer-auto-theme, .odometer.odometer-theme-car {
  -moz-border-radius: 0.34em;
  -webkit-border-radius: 0.34em;
  border-radius: 0.34em;
  font-family: "Arimo", monospace;
  padding: 0.15em;
  background: #000;
  color: #eee0d3;
}
.odometer.odometer-auto-theme .odometer-digit, .odometer.odometer-theme-car .odometer-digit {
  -moz-box-shadow: inset 0 0 0.3em rgba(0, 0, 0, 0.8);
  -webkit-box-shadow: inset 0 0 0.3em rgba(0, 0, 0, 0.8);
  box-shadow: inset 0 0 0.3em rgba(0, 0, 0, 0.8);
  background-image: url('data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4gPHN2ZyB2ZXJzaW9uPSIxLjEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGRlZnM+PGxpbmVhckdyYWRpZW50IGlkPSJncmFkIiBncmFkaWVudFVuaXRzPSJvYmplY3RCb3VuZGluZ0JveCIgeDE9IjAuNSIgeTE9IjAuMCIgeDI9IjAuNSIgeTI9IjEuMCI+PHN0b3Agb2Zmc2V0PSIwJSIgc3RvcC1jb2xvcj0iIzMzMzMzMyIvPjxzdG9wIG9mZnNldD0iNDAlIiBzdG9wLWNvbG9yPSIjMzMzMzMzIi8+PHN0b3Agb2Zmc2V0PSI2MCUiIHN0b3AtY29sb3I9IiMxMDEwMTAiLz48c3RvcCBvZmZzZXQ9IjgwJSIgc3RvcC1jb2xvcj0iIzMzMzMzMyIvPjxzdG9wIG9mZnNldD0iMTAwJSIgc3RvcC1jb2xvcj0iIzMzMzMzMyIvPjwvbGluZWFyR3JhZGllbnQ+PC9kZWZzPjxyZWN0IHg9IjAiIHk9IjAiIHdpZHRoPSIxMDAlIiBoZWlnaHQ9IjEwMCUiIGZpbGw9InVybCgjZ3JhZCkiIC8+PC9zdmc+IA==');
  background-size: 100%;
  background-image: -webkit-gradient(linear, 50% 0%, 50% 100%, color-stop(0%, #333333), color-stop(40%, #333333), color-stop(60%, #101010), color-stop(80%, #333333), color-stop(100%, #333333));
  background-image: -moz-linear-gradient(top, #333333 0%, #333333 40%, #101010 60%, #333333 80%, #333333 100%);
  background-image: -webkit-linear-gradient(top, #333333 0%, #333333 40%, #101010 60%, #333333 80%, #333333 100%);
  background-image: linear-gradient(to bottom, #333333 0%, #333333 40%, #101010 60%, #333333 80%, #333333 100%);
  padding: 0 0.15em;
}
.odometer.odometer-auto-theme .odometer-digit:first-child, .odometer.odometer-theme-car .odometer-digit:first-child {
  -moz-border-radius: 0.2em 0 0 0.2em;
  -webkit-border-radius: 0.2em;
  border-radius: 0.2em 0 0 0.2em;
}
.odometer.odometer-auto-theme .odometer-digit:last-childd, .odometer.odometer-theme-car .odometer-digit:last-childd {
  -moz-border-radius: 0 0.2em 0.2em 0;
  -webkit-border-radius: 0;
  border-radius: 0 0.2em 0.2em 0;
  background-image: url('data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4gPHN2ZyB2ZXJzaW9uPSIxLjEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGRlZnM+PGxpbmVhckdyYWRpZW50IGlkPSJncmFkIiBncmFkaWVudFVuaXRzPSJvYmplY3RCb3VuZGluZ0JveCIgeDE9IjAuNSIgeTE9IjAuMCIgeDI9IjAuNSIgeTI9IjEuMCI+PHN0b3Agb2Zmc2V0PSIwJSIgc3RvcC1jb2xvcj0iI2VlZTBkMyIvPjxzdG9wIG9mZnNldD0iNDAlIiBzdG9wLWNvbG9yPSIjZWVlMGQzIi8+PHN0b3Agb2Zmc2V0PSI2MCUiIHN0b3AtY29sb3I9IiNiYmFhOWEiLz48c3RvcCBvZmZzZXQ9IjgwJSIgc3RvcC1jb2xvcj0iI2VlZTBkMyIvPjxzdG9wIG9mZnNldD0iMTAwJSIgc3RvcC1jb2xvcj0iI2VlZTBkMyIvPjwvbGluZWFyR3JhZGllbnQ+PC9kZWZzPjxyZWN0IHg9IjAiIHk9IjAiIHdpZHRoPSIxMDAlIiBoZWlnaHQ9IjEwMCUiIGZpbGw9InVybCgjZ3JhZCkiIC8+PC9zdmc+IA==');
  background-size: 100%;
  background-image: -webkit-gradient(linear, 50% 0%, 50% 100%, color-stop(0%, #eee0d3), color-stop(40%, #eee0d3), color-stop(60%, #bbaa9a), color-stop(80%, #eee0d3), color-stop(100%, #eee0d3));
  background-image: -moz-linear-gradient(top, #eee0d3 0%, #eee0d3 40%, #bbaa9a 60%, #eee0d3 80%, #eee0d3 100%);
  background-image: -webkit-linear-gradient(top, #eee0d3 0%, #eee0d3 40%, #bbaa9a 60%, #eee0d3 80%, #eee0d3 100%);
  background-image: linear-gradient(to bottom, #eee0d3 0%, #eee0d3 40%, #bbaa9a 60%, #eee0d3 80%, #eee0d3 100%);
  background-color: #eee0d3;
  color: #000;
}
.odometer.odometer-auto-theme .odometer-digit .odometer-digit-inner, .odometer.odometer-theme-car .odometer-digit .odometer-digit-inner {
  left: 0.15em;
}
.odometer.odometer-auto-theme.odometer-animating-up .odometer-ribbon-inner, .odometer.odometer-auto-theme.odometer-animating-down.odometer-animating .odometer-ribbon-inner, .odometer.odometer-theme-car.odometer-animating-up .odometer-ribbon-inner, .odometer.odometer-theme-car.odometer-animating-down.odometer-animating .odometer-ribbon-inner {
  -webkit-transition-timing-function: linear;
  -moz-transition-timing-function: linear;
  -ms-transition-timing-function: linear;
  -o-transition-timing-function: linear;
  transition-timing-function: linear;
}
</style>
<script>

window.odometerOptions = {
  auto: true, // Don't automatically initialize everything with class 'odometer'
  duration: 4000, // Change how long the javascript expects the CSS animation to take
  theme: 'car', // Specify the theme (if you have more than one theme css file on the page)
  animation: 'count' // Count is a simpler animation method which just increments the value,
                     // use it when you're looking for something more subtle.
};
 



start = <?= $start ?>;
change_per_day=<?= $change_per_day ?>;
a = new Date;
var start_time = new Date(a.toDateString()).getTime() / 1000;
function change(){
  var seconds = new Date().getTime() / 1000;
  diff=seconds-start_time;
document.getElementById('element').innerHTML = start + diff*change_per_day/(3600*24);
setTimeout("change()",2000)
  
}

change();
</script>

<div class="result" style="margin-top:30px">
    </div>

    <script>
      show_main();
    </script>

    <p style="margin-top:30px;color:darkgray;">(Data from <a style="color:darkgray" href="https://www.ncbi.nlm.nih.gov/sra/docs/sragrowth/">NCBI</a>, with some interpolation.)