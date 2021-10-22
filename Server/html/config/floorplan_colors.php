<?php

return [
  'Colors' => [
    'sensor_types' => [
      'Waterproof Temperature' => 'rgba(0,0,0,0)',
      'Humidity' => 'rgba(0,0,0,0)',
      'Temperature' => 'rgba(0,0,0,0)',
      'Co2' => 'rgba(0,0,0,0)',
      'pH' => 'rgba(0,0,0,0)',
      'DO' => 'rgba(0,0,0,0)',
      'EC' => 'rgba(0,0,0,0)',
      'CT' => 'rgba(0,0,0,0)',
      'Fill Level' => 'rgba(0,0,0,0)',
      'PAR' => 'rgba(0,0,0,0)'
    ],
    'map_item_types' => [
      'Walls' => '#000000',
      'Sensor' => '#52B965',
      'Trays' => '#231F20',
      'Plants' => '#231F20',
      'Plant Placeholder' => '#999999',
      'Plant_Placeholders' => '#999999',
      'Doors' => '#000000',
      'Server_Switches' => '#8D68AC',
      'Power_Panel' => '#F6EB0F',
      'Res_Devices' => '#00ADEE',
      'Appliances' => '#DDDDDD',
      'Pumps' => '#F6EB0F',
      'Solatube' => '#00ADEE',
      'Lights' => '#D24899',
      'Fans' => '#D3D3D3',
      'Dehum' => '#D3D3D3',
      'HVAC' => '#D3D3D3',
      'Zone' => '#00FFFF'
    ],
    'sensor_type_values' => [
      'Humidity' => [
        90 => '#287EF0',
        80 => '#3F8DEA',
        70 => '#4B94E8',
        60 => '#579CE5',
        50 => '#63A3E3',
        40 => '#6FABE0',
        30 => '#7BB2DD',
        20 => '#87BADB',
        10 => '#93C1D8',
        0  => '#9FC9D6'
      ],
      'BME280 Humidity' => [
        90 => '#287EF0',
        80 => '#3F8DEA',
        70 => '#4B94E8',
        60 => '#579CE5',
        50 => '#63A3E3',
        40 => '#6FABE0',
        30 => '#7BB2DD',
        20 => '#87BADB',
        10 => '#93C1D8',
        0  => '#9FC9D6'
      ],
      'SCD30 Humidity' => [
        90 => '#287EF0',
        80 => '#3F8DEA',
        70 => '#4B94E8',
        60 => '#579CE5',
        50 => '#63A3E3',
        40 => '#6FABE0',
        30 => '#7BB2DD',
        20 => '#87BADB',
        10 => '#93C1D8',
        0  => '#9FC9D6'
      ],
      'Temperature' => [
        35 => '#800026',
        32 => '#BD0026',
        30 => '#E31A1C',
        27 => '#FC4E2A',
        24 => '#FD8D3C',
        21 => '#FEB24C',
        18 => '#FED976',
        15 => '#FFEDA0'
      ],
      'BME280 Air Temperature' => [
        35 => '#800026',
        32 => '#BD0026',
        30 => '#E31A1C',
        27 => '#FC4E2A',
        24 => '#FD8D3C',
        21 => '#FEB24C',
        18 => '#FED976',
        15 => '#FFEDA0'
      ],
      'SCD30 Air Temperature' => [
        35 => '#800026',
        32 => '#BD0026',
        30 => '#E31A1C',
        27 => '#FC4E2A',
        24 => '#FD8D3C',
        21 => '#FEB24C',
        18 => '#FED976',
        15 => '#FFEDA0'
      ],
      'LoRa temp' => [
        35 => '#800026',
        32 => '#BD0026',
        30 => '#E31A1C',
        27 => '#FC4E2A',
        24 => '#FD8D3C',
        21 => '#FEB24C',
        18 => '#FED976',
        15 => '#FFEDA0'
      ],
      'SCD30 Co2' => [
        1200 => '#17740B',
        1100 => 'rgb(62, 128, 25)',
        1000 => '#7EA93C',
        900 => '#8FB355',
        800 => '#A0BE6E',
        700 => '#B1C987',
        600 => '#C2D3A0',
        500 => '#D3DEB9',
        400 => '#E5E9D3'
      ],
      'Co2' => [
        1200 => '#17740B',
        1100 => 'rgb(62, 128, 25)',
        1000 => '#7EA93C',
        900 => '#8FB355',
        800 => '#A0BE6E',
        700 => '#B1C987',
        600 => '#C2D3A0',
        500 => '#D3DEB9',
        400 => '#E5E9D3'
      ],
      'CT' => [
        1.0 => '#94180A',
        0.9 => '#9C2D10',
        0.8 => '#A44217',
        0.7 => '#AC571E',
        0.6 => '#B46C25',
        0.5 => '#BC812B',
        0.4 => '#C49632',
        0.3 => '#CCAB39',
        0.2 => '#DCD547',
        0.1 => '#E4DE6C'
      ],
      'PAR' => [
        2000 => '#ff3f00',
        1750 => '#ff5c00',
        1500 => '#ff7400',
        1250 => '#fe8a00',
        1000 => '#fb9e00',
        700 => '#f7b100',
        500 => '#f2c300',
        250 => '#ecd400',
        0 => '#B5B5B5'
      ],
      'LoRa PAR' => [
        2000 => '#ff3f00',
        1750 => '#ff5c00',
        1500 => '#ff7400',
        1250 => '#fe8a00',
        1000 => '#fb9e00',
        700 => '#f7b100',
        500 => '#f2c300',
        250 => '#ecd400',
        0 => '#B5B5B5'
      ],
      'LoRa lux' => [
        6000 => '#ff3f00',
        5250 => '#ff5c00',
        4500 => '#ff7400',
        3750 => '#fe8a00',
        3000 => '#fb9e00',
        2100 => '#f7b100',
        1650 => '#f2c300',
        750 => '#ecd400',
        0 => '#B5B5B5'
      ],
      'EC' => [
        2.8 => '#398100',
        2.5 => '#4a8f2a',
        2.2 => '#5b9d46',
        1.9 => '#6cac60',
        1.6 => '#7fba78',
        1.3 => '#92c891',
        1.0 => '#a6d6a9',
        0.7 => '#bbe4c1',
        0.4 => '#d1f1d8',
        0.1 => '#e9ffef'
      ],
      'LoRa electrical_conductivity' => [
        2.8 => '#398100',
        2.5 => '#4a8f2a',
        2.2 => '#5b9d46',
        1.9 => '#6cac60',
        1.6 => '#7fba78',
        1.3 => '#92c891',
        1.0 => '#a6d6a9',
        0.7 => '#bbe4c1',
        0.4 => '#d1f1d8',
        0.1 => '#e9ffef'
      ],
      'LoRa volumetric_water_content' => [
        100 => '#002D68',
        90 => '#003c79',
        80 => '#004c89',
        70 => '#005d99',
        60 => '#006da8',
        50 => '#007eb7',
        40 => '#008fc5',
        30 => '#00a1d1',
        20 => '#00b2de',
        10 => '#00c4e9'
      ],
      'LoRa GWC' => [
        100 => '#002D68',
        90 => '#003c79',
        80 => '#004c89',
        70 => '#005d99',
        60 => '#006da8',
        50 => '#007eb7',
        40 => '#008fc5',
        30 => '#00a1d1',
        20 => '#00b2de',
        10 => '#00c4e9'
      ],
      'RSSI' => [
        -10 => '#00ff58',
        -20 => '#69ee19',
        -30 => '#90db00',
        -40 => '#abc800',
        -50 => '#c1b300',
        -60 => '#d49c00',
        -70 => '#e28400',
        -80 => '#ed6800',
        -90 => '#f34600',
        -100 => '#f60000'
      ],
      'LoRa battery_level' => [
        2.8 => '#00ff58',
        2.5 => '#69ee19',
        2.2 => '#90db00',
        1.9 => '#abc800',
        1.6 => '#c1b300',
        1.3 => '#d49c00',
        1.0 => '#e28400',
        0.7 => '#ed6800',
        0.4 => '#f34600',
        0.1 => '#f60000'
      ],
      'data_type_values' => [
        'Humidity' => [
          90 => '#287EF0',
          80 => '#3F8DEA',
          70 => '#4B94E8',
          60 => '#579CE5',
          50 => '#63A3E3',
          40 => '#6FABE0',
          30 => '#7BB2DD',
          20 => '#87BADB',
          10 => '#93C1D8',
          0  => '#9FC9D6'
        ],                                                  
        'Air Temperature' => [
          35 => '#800026',
          32 => '#BD0026',
          30 => '#E31A1C',
          27 => '#FC4E2A',
          24 => '#FD8D3C',
          21 => '#FEB24C',
          18 => '#FED976',
          15 => '#FFEDA0'
        ],
        'Soil Temp' => [
          35 => '#800026',
          32 => '#BD0026',
          30 => '#E31A1C',
          27 => '#FC4E2A',
          24 => '#FD8D3C',
          21 => '#FEB24C',
          18 => '#FED976',
          15 => '#FFEDA0'
        ],
        'Co2' => [
          1200 => '#17740B',
          1100 => 'rgb(62, 128, 25)',
          1000 => '#7EA93C',
          900 => '#8FB355',
          800 => '#A0BE6E',
          700 => '#B1C987',
          600 => '#C2D3A0',
          500 => '#D3DEB9',
          400 => '#E5E9D3'
        ],
        'CT' => [
          1.0 => '#94180A',
          0.9 => '#9C2D10',
          0.8 => '#A44217',
          0.7 => '#AC571E',
          0.6 => '#B46C25',
          0.5 => '#BC812B',
          0.4 => '#C49632',
          0.3 => '#CCAB39',
          0.2 => '#DCD547',
          0.1 => '#E4DE6C'
        ],
        'PAR' => [
          2000 => '#ff3f00',
          1750 => '#ff5c00',
          1500 => '#ff7400',
          1250 => '#fe8a00',
          1000 => '#fb9e00',
          700 => '#f7b100',
          500 => '#f2c300',
          250 => '#ecd400',
          0 => '#B5B5B5'
        ],
        'Lux' => [
          6000 => '#ff3f00',
          5250 => '#ff5c00',
          4500 => '#ff7400',
          3750 => '#fe8a00',
          3000 => '#fb9e00',
          2100 => '#f7b100',
          1650 => '#f2c300',
          750 => '#ecd400',
          0 => '#B5B5B5'
        ],
        'EC' => [
          2.8 => '#398100',
          2.5 => '#4a8f2a',
          2.2 => '#5b9d46',
          1.9 => '#6cac60',
          1.6 => '#7fba78',
          1.3 => '#92c891',
          1.0 => '#a6d6a9',
          0.7 => '#bbe4c1',
          0.4 => '#d1f1d8',
          0.1 => '#e9ffef'
        ],
        'Volumetric Water Content' => [
          100 => '#002D68',
          90 => '#003c79',
          80 => '#004c89',
          70 => '#005d99',
          60 => '#006da8',
          50 => '#007eb7',
          40 => '#008fc5',
          30 => '#00a1d1',
          20 => '#00b2de',
          10 => '#00c4e9'
        ],
        'Gravimetric Water Content' => [
          100 => '#002D68',
          90 => '#003c79',
          80 => '#004c89',
          70 => '#005d99',
          60 => '#006da8',
          50 => '#007eb7',
          40 => '#008fc5',
          30 => '#00a1d1',
          20 => '#00b2de',
          10 => '#00c4e9'
        ],
        'RSSI' => [
          -10 => '#00ff58',
          -20 => '#69ee19',
          -30 => '#90db00',
          -40 => '#abc800',
          -50 => '#c1b300',
          -60 => '#d49c00',
          -70 => '#e28400',
          -80 => '#ed6800',
          -90 => '#f34600',
          -100 => '#f60000'
        ],
        'Battery Level' => [
          2.8 => '#00ff58',
          2.5 => '#69ee19',
          2.2 => '#90db00',
          1.9 => '#abc800',
          1.6 => '#c1b300',
          1.3 => '#d49c00',
          1.0 => '#e28400',
          0.7 => '#ed6800',
          0.4 => '#f34600',
          0.1 => '#f60000'
        ]
      
    ]
  ]
];
