
$(document).ready(function() {
var chart = AmCharts.makeChart( "pie", {
"type": "pie",
"theme": "light",
"minWidth": 200,
"maxWidth": 'auto',
"maxHeight": 'auto',
"minHeight": 200,
"dataProvider": [ {
  "country": "Paracetamol",
  "litres": 501.9
}, {
  "country": "Riopan",
  "litres": 301.9
}, {
  "country": "Omeprazol",
  "litres": 201.1
}, {
  "country": "Ranitidína inyectable",
  "litres": 99
}, {
  "country": "Belgium",
  "litres": 60
}, {
  "country": "The Netherlands",
  "litres": 50
} ],
"valueField": "litres",
"titleField": "country",
 "balloon":{
 "fixedPosition":true
},
"export": {
  "enabled": true
},
  "responsive": {
    "enabled": true
}
});
var chart2 = AmCharts.makeChart( "pie2", {
"type": "pie",
"theme": "light",
"minWidth": 200,
"maxWidth": 400,
"maxHeight": 400,
"minHeight": 200,
"dataProvider": [ {
  "country": "Lithuania",
  "litres": 501.9
}, {
  "country": "Czech Republic",
  "litres": 301.9
}, {
  "country": "The Netherlands",
  "litres": 50
} ],
"valueField": "litres",
"titleField": "country",
 "balloon":{
 "fixedPosition":true
},
"export": {
  "enabled": true
},
  "responsive": {
    "enabled": true
}
});
var chart3 = AmCharts.makeChart( "pie3", {
"type": "pie",
"theme": "light",
"minWidth": 200,
"maxWidth": 400,
"maxHeight": 400,
"minHeight": 200,
"dataProvider": [ {
  "country": "Lithuania",
  "litres": 501.9
}, {
  "country": "Czech Republic",
  "litres": 301.9
}, {
  "country": "The Netherlands",
  "litres": 50
} ],
"valueField": "litres",
"titleField": "country",
 "balloon":{
 "fixedPosition":true
},
"export": {
  "enabled": true
},
  "responsive": {
    "enabled": true
}
});
});