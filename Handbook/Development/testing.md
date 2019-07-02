# Manual QA

## Documenting Your Tests

The preferred method of documentation is screen-capture with audio commentary and written notes. To do this most easily use the [loom chrome extension](http://useloom.com) and make a short test video first with the demo open to see if audio and video are recording correctly. record 30s to 1m and check. If you prefer more control and reliable recording use OBS ([Open Broadcaster System](https://obsproject.com/))


## Pre Test Checklist
* [ ] Record a short test video to make sure audio and video are working. 
* [ ] Duplicate & Update the previous QA Notes G-Doc in All Company/Meeting Notes/QA

## User Flows

### Password Reset

* [ ] Login as a known user (admin) to be sure you have the correct 'old' password.
* [ ] Create a new user under settings/users. Set the email address to an email that you, and only you, can receive email at (e.g. first.l@grownetics.co) save the new user.
* [ ] Logout.
* [ ] Click 'Reset Password'.
* [ ] Type in the email of the user, hit submit.
* [ ] Check your email, follow the link included (check the domain part of the link and make sure it matches the test domain you're in, if not copy paste the domain to match your test environment)
* [ ] Submit non-matching passwords, ensure it fails back to the form.
* [ ] Submit new matching passwords.
* [ ] Login with the new password.

### Data Visualization

* [ ] Run GrowFaker for a bit and see fake data on the map dashboard (it's what generates fake data for the sensors on the map, it may already be running). 
* [ ] If GrowFaker isn't running go to settings/devices and at the top switch the mode to demo.
* [ ] Check the Dash Map has data in real-time.
* [ ] Check the Dash Charts have air temp, humidity, and Co2 data in real-time. Make a new dash chart.
* [ ] Check red/green data pulses are displaying when new data comes in.
* [ ] Check the large Chart at charts/view and make sure a Device's historical data loads.
* [ ] Check the large Chart at charts/view and make sure a Zone's historical data loads with each data type Humidity, 
* [ ] Check the Harvest Batch chart and make sure a Batch's environemental data loads.Temperature, Co2, and Vapor Pressure Deficit.

### Owner Managing Users

* [ ] Login as the `grower@grownetics.co` user
* [ ] Click 'Settings' in the nav and make sure 'Manage Users' is not visible.
* [ ] Try and access /users/, this should fail.
* [ ] Try and access /users/edit/1 the admin user, this should also fail.
* [ ] Logout
* [ ] Login as the `owner@grownetics.co` user
* [ ] Click 'Settings' in the nav, then click 'Manage Users'.
* [ ] Ensure the 'Admin' user is not visible in the list.
* [ ] Edit a user and save the change.
* [ ] Try and access /users/edit/1 the admin user, this should fail. Sometimes I get demo for user 1 so I try user 2 as well.

### Batch Workflow Panel

* [ ] Create a Strain and Recipe (with at least 1 entry).
* [ ] Create a new Batch with at least 1 Plant.
* [ ] Enter a weight for the plant and the batch.
* [ ] Check that the units are correct and try changing your unit preference in account settings and check the values have changed.
* [ ] Complete the Batch's Plant task.
* [ ] Check that the Plant is planted in the Plants Table and there is a Plant on the Dash map.
* [ ] Add a new Move task to the batch with a Bench zone as the destination.
* [ ] Complete the new task and ensure the Batch is planted in the correct Zone. 
* [ ] Check the Gantt Chart and make sure the timeline is correct


## Dashboard Checks

* [ ] Login
* [ ] Check all data types loading on mapped dashboard, click through all map dash options.
* [ ] Check colors on data types
* [ ] Check zone labels
* [ ] Check sensor device ID order is correct
* [ ] Send several Chat messages
* [ ] Check dashboard charts by editing and creating, destroying some.
* [ ] Check Plants and Walls load in front of other map elements.

## Workflow Checks

* [ ] Click on Workflow
* [ ] Add New Facility Task
* [ ] Attach a date in the current month
* [ ] Go to the Dashboard
* [ ] Check for the task on the Calendar, and click it
* [ ] You should land on the Tasks workflow page.

#### PAR Sensor Check

* [ ] Navigate to `/sensors/add` and add a sensor of type PAR (or verify at least one PAR sensor exists in the database).
* [ ] Reload the dashboard.
* [ ] Verify that a PAR option appears in the layer control of the map.
* [ ] Verify notification was added: "your user added a sensor"

## Strain and Batch Creation Flow

* [ ] Create a cultivar, delete a cultivar, edit a cultivar, open cultivar profile
* [ ] Create a cultivar specific recipe that moves through all zones
* [ ] Create a non cultivar specific recipe
* [ ] Create a batch with a range of plants and a plant list and make sure the Plant ID's are correct.
* [ ] Create a batch with the recipe and plants and move it through all zones to completion
* [ ] Check that the Plants are planted in the Plants Table and there are Plants on the Dash map.
* [ ] Enter a weight for the Plant, reload the page, and make sure the weight you entered is still there.
* [ ] Create several batches and complete their tasks with the start date prior to today, after today, today.
* [ ] Check durations in Gantt match the recipes
* [ ] Edit a batch/complete a task and check if gantt and active batches page correctly reflect these changes.
* [ ] Check changes against batch profile and active batches page.
* [ ] Check Charts
* [ ] Check Wiki - make  a wiki, edit wiki.


## Manual Set Point Override
1. Make sure there is a set point of the type you want to test in a zone or plant zone.
2. Go to `/zones` and verify that an input field exists for the set point.
3. Edit the value of the set point in the input field, and click the green check icon.
4. Verify that value is saved after the spinner stops spinning.

## Recipe Creation Flow
* [ ] Create a recipe
* [ ] Add multiple recipe entries (at least 3)
* [ ] Add multiple sub tasks to each entry (at least 3 sub tasks per 1 entry) with at least 1 sub task having days = the recipe entry's start date, days = recipe entry's end date, and days somewhere in between. E.g. If the first recipe entry in the recipe is "live in Clone for 14 days", add a sub task on day 0, day 14, and day 10.
* [ ] Create a batch with the recipe and confirm the batch workflow panel on the batch's profile has correct dates and task information


## Data Processing Checks

### Batch Data Per Zone Check
* [ ] Log into chronograf verify sensor_data.autogen is populated
* [ ] Log into dashboard and verify you have a device in an active zone
* [ ] Go to an active harvest batch and plant batch in active zone from Step #2
* [ ] Go back to chronograf and verify you have a '3' under source_type

## Floorplan Import
* [ ] Go to `/floorplans/add`
* [ ] Upload a Floorplan SVG
* [ ] Wait a bit...
* [ ] Go to `/zones` and verify that zone names do not contain dashes, underscores, etc.

