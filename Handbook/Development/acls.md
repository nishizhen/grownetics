# ACLs

ACLs or Access Control Lists allow us to have granular control over who is allowed to access what.

Our ACL implementation is simple, `AppController.php::isAuthorized` contains all of it.

An ACL says whether or not a certain role is allowed to access a certain controller/action combo.

A role is a grouping of ACLs.

So users with different roles have differing levels of access based on which ACLs
that particular role is associated with.

## Updating the ACLS Seeds

First consult the documented located at: https://docs.google.com/spreadsheets/d/1PpH7cpXojQVgYYv0iSVKGa6PlODaxfzZvVaWAAOwNck/edit#gid=0

The ID's of the ACL in `AclsSeeds.php` should **always** match with the row numbers in the spreadsheet above

If you are adding or deleting an ACL from `AclsSeeds.php` then you need to update line 28 of `AclsRolesSeed.php`
The range on line 28 must go from the lowest ACL ID to the highest ACL ID present in `AclsSeeds.php`
