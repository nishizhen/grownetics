# Batches and Plants

## Plant Movement

Plant placeholders are locations on the map that plants can move to.

The PlantsTable has a `getPlaceholders` function to get the available locations
for a given zone or bench.

When Move and Harvest tasks are completed, or when a user manually moves a batch,
the PlantsTable `movePlants` function is called.