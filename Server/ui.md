[< GrowServer](README.md)

# Dashboard

The dashboard is the main entrypoint to the GrowServer User Interface. It is the first page users will see after they 
log in. It contains multiple UI widgets that expose most of the information collected by the Grownetics system, including
an interactive map, multiple charts, as well as chat and notification widgets.

# Map

The facility map shows a 2D view of a client's installation, with real-time updates of sensor data.

## Interactive (Data) Layers

### Temperature

There are typically two temperature layers, High and Low, based on the height at which the sensors are placed when we install them.

### Humidity

### CO2

### PAR (Light spectrum sensor data)

If a facility contains 1 or more PAR sensors, this option should appear in the layer control on the map.

A PAR Sensor can be added to an existing device via the `/sensors/add` page in GrowServer.

PAR sensors should be connected to a device on pins `A0` or `A1`.

## Background Layers

### Walls

### Zones

### Plant Placeholders

Shows the locations of places which could contain a plant, but is currently empty. Used by the batch workflow to determine 
where plants should be moved to.
