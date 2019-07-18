<?php
use Migrations\AbstractSeed;

/**
 * AclsRoles seed.
 */
class AclsRolesSeed extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * http://docs.phinx.org/en/latest/seeding.html
     *
     * @return void
     */
    public function run()
    {
        # These ACL IDs come from the AclsSeed.php file.
        # All ACL info is maintained here: https://docs.google.com/spreadsheets/d/1PpH7cpXojQVgYYv0iSVKGa6PlODaxfzZvVaWAAOwNck/edit#gid=0
        $this->execute('TRUNCATE acls_roles');

        # These numbers correspond to the RED N cells in the doc above
        $owner = [];
        # When adding ACLS make sure to increment the number below, it should equal the highest ACL ID available.
        $all_acls = range(1, 75);
        $grower_forbidden_acls = [
            66
        ];
        $user_forbidden_acls = [
            42,
            43,
            44,
            47,
            48,
            49,
            52,
            53,
            54,
            61,
            65,
            66
        ];
        $guest_forbidden_acls = [
            3,
            4,
            5,
            13,
            14,
            15,
            16,
            17,
            18,
            19,
            20,
            21,
            22,
            25,
            26,
            27,
            31,
            32,
            33,
            36,
            37,
            38,
            39,
            42,
            43,
            44,
            47,
            48,
            49,
            52,
            53,
            54,
            56,
            57,
            58,
            61,
            62,
            65,
            66,
            67,
            68,
            70,
            71,
            72,
            73,
            74
        ];
        # First populate all the ACLs
        $user_acls = $all_acls;
        $guest_acls = $all_acls;
        $grower_acls = $all_acls;
        $owner_acls = $all_acls;

        # Now remove those we don't want to allow
        foreach ($user_forbidden_acls as $acl)
            unset($user_acls[$acl-1]);
        foreach ($guest_forbidden_acls as $acl)
            unset($guest_acls[$acl-1]);
        foreach ($grower_forbidden_acls as $acl)
            unset($grower_acls[$acl-1]);

        $data = [];

        # Owner - 2
        foreach ($owner_acls as $acl) {
            array_push($data, [
                'acl_id' => $acl,
                'role_id' => 2,
            ]);
        }

        # Grower - 3
        foreach ($grower_acls as $acl) {
            array_push($data, [
                'acl_id' => $acl,
                'role_id' => 3,
            ]);
        }

        # User - 4
        foreach ($user_acls as $acl) {
            array_push($data, [
                'acl_id' => $acl,
                'role_id' => 4,
            ]);
        }

        # Guest - 6
        foreach ($guest_acls as $acl) {
            array_push($data, [
                'acl_id' => $acl,
                'role_id' => 6,
            ]);
        }

        $table = $this->table('acls_roles');
        $table->insert($data)->save();
    }
}
