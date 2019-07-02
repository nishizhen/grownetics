When users are logging into growdash, they now have an option that keeps the user logged in for 30 days.

When a user logs in and opts into the stay-logged-in feature, we create a unique key and store it in
the users table, under the `access_code` column.  We then write this key and their username to a hashed
cookie which is stored on the users browser.  

When the user comes back to the dash after opting into this
feature, the cookie is read and the key is compared with the `access_code` stored in the DB.  If these
two keys match, the user is automatically logged in. 