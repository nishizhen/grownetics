# UI & UX

## [Grownetics Style Guide](style-guide.pdf)

### Example Metrc Plant Tag ID's  
1A400031268752F000014560  
1A4000BN2UE28F000003520  
1A4000LU72M09E000054350  
1A4000FT24UE28F000579800  
1A400091NWEK2E000579800


### Dashboard Map SVGs  

##### How to make Dashboard Maps
Currently shapes in layers must be made in the correct order to match the device ID. Same for plants to populate in the correct order.


##### Final Layered Integration Map SVGs are in each Clients google drive folder 
[Clients](clients) 

##### ProtoGrow Demo/Staging SVGs 
[ProtoGrow Map Dash Files](https://drive.google.com/open?id=0B7G5Gc0AHKKydTFsX1FCV2E5YjA)  

#### Staging server Floorplan import URL
(http://staging.cropcircle.io/floorplans/add)

#### Grouping Zones

Start by creating a single group labeled 'Zones'. Inside this group should be the following groups:

**Important:** Ordering! Make sure the HVAC group is the last group within the Zones group. This will ensure that Plant Placeholders are correctly associated with Room/Group Zones instead of HVAC Zones.

1. 'Plant Zone'; inside this group, add all &lt;rect&gt; elements that most closely surround a group of plants. E.g. Benches, clone shelves, jar cure shelves, trim benches. Anywhere that the plants will consistently be.

2. 'Room'; this group is unique in that it's &lt;rect&gt; elements should be areas that should still be labeled and displayed on the Dashboard map but are not being monitored (no 3D Crop Sensor in area). Essentially, these are Custom areas that do not belong in one of the other groups. E.g. Res Tanks, Nutrient tanks, Co2 tanks, Mechanical, Electrical Room. 

3. 'Clone'; add all Clone Rooms to this group. These &lt;rect&gt; elements must be the entire room, not just a section of it. E.g. Clone Room, Tissue Culture.

4. 'Veg'; add all Veg Rooms to this group. Again, these must cover the entire room. E.g. Veg 1, Baby Veg.

5. 'Bloom'; add all Bloom Rooms to this group. E.g. Flower 1, Final Flush Room.

6. 'Dry'; ...

7. 'Cure'; ...

8. 'Processing'; ...

9. 'Storage'; ...

10. 'Shipping'; add all Shipping Rooms to this group. E.g. Shipping Bay 1.

11. 'HVAC'; add all &lt;rect&gt; elements that represent a section of HVAC to this group.

and that's it, the importer will be able to correctly identify if a Zone is a Room, HVAC, Group, or Custom and it's plant-zone-type-id.

How the .svg is represented in the database:

  * All &lt;rect&gt; elements in 'Room' will have a zone_type of Custom and a null plant-zone-type ID.

  * All &lt;rect&gt; elements in Clone, Veg, Bloom, Dry, Cure, Processing, Storage, and Shipping will have a zone_type of Room and it's plant-zone-type ID = the group's label (therefore it's important that the group's label is typed correctly and matches one of the 8 plant-zone-types).

  * All &lt;rect&gt; elements in 'Plant Zone' will have a zone_type of Group and inherit it's plant-zone-type ID from the parent Room. 

  * All &lt;rect&gt; elements in 'HVAC' will have a zone_type of HVAC and inherit it's plant-zone-type ID from the parent Room.

**If you want to allow a Room to hold an infinite number of plants, simply don't place any plant_placeholders in that Room, but still create a Plant Zone <rect> element within that room. Plants will automatically fill into benches when moved into Rooms with a set plant-zone-type ID or when moved directly in to a Plant Zone (Group) will automatically fill into the available spots within that Group. **

**Note:** &lt;rect&gt; elements can be thought of as the actual Zone object in the database, the label's of the groups containing a set of &lt;rect&gt; elements is the zone-type and plant-zone-type of those elements. 

Example XML: [G Drive Client Dashboard Maps Example XML](https://drive.google.com/open?id=0B7PzM5VQjD4NRk5WQUdZNEJjZUU)


### GeoJSON / SVG Floorplan Import Group IDs

* Room Names  
* Zones (Pull all group names under zones)  
    * Zone_Type *see above
         * Zone (rect id => zone_id)
* Appliances
    * HVAC
    * Dehum
    * Fans
    * Lights
    * Solatube 
    * Pumps

* G-Devices  
    * Res Devices             Res_Devices  
    * Power Panel             Power_Panel  
    * Server Switches       Server_Switches  
    * Crop Sensor             Crop_Sensor  

* Plant Placeholders
* G-Floorplan  
    * Doors
    * Walls
* Scale  
* Client Floorplan  

## Resources
[Adobe Ai SVG Page](https://helpx.adobe.com/illustrator/using/svg.html)
