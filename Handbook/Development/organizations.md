# Organizations

Grownetics uses Organizations to group together users, facilities, and floorplans.

## Behavior

Any records that should be restricted by Organization, should use the
`OrganizationBehavior`.

## Invites

Organization invites are created by the OrganizationsTable. There is no separate
`invitiation` record anywhere. An invite instead is an entry in the `users_roles`
table with the `organization_id` field set and the `role_id` set to 
`Organization Invitee`.

When the user accepts this invite, the role is chaged to `Organization Member`.