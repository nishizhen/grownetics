$().ready(function() {
var doughnutData = [
	{
		value: 60,
		color:"#1c9ca7"
	},
	{
		value : 40,
		color : "#f68275"
	}
];
var doughnutData2 = [
	{
		value: 40,
		color:"#1c9ca7"
	},
	{
		value : 60,
		color : "#f68275"
	}
];
var myDoughnut = new Chart(document.getElementById("serverstatus1").getContext("2d")).Doughnut(doughnutData);
var myDoughnut = new Chart(document.getElementById("serverstatus2").getContext("2d")).Doughnut(doughnutData2);
var myDoughnut = new Chart(document.getElementById("serverstatus3").getContext("2d")).Doughnut(doughnutData);
var myDoughnut = new Chart(document.getElementById("serverstatus4").getContext("2d")).Doughnut(doughnutData2);
var myDoughnut = new Chart(document.getElementById("serverstatus5").getContext("2d")).Doughnut(doughnutData);
var myDoughnut = new Chart(document.getElementById("serverstatus6").getContext("2d")).Doughnut(doughnutData2);
});