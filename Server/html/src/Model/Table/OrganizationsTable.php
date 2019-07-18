<?php

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\ORM\TableRegistry;

/**
 * Organizations Model
 *
 * @property \App\Model\Table\UsersTable|\Cake\ORM\Association\BelongsTo $Users
 * @property \App\Model\Table\UsersRolesTable|\Cake\ORM\Association\HasMany $UsersRoles
 *
 * @method \App\Model\Entity\Organization get($primaryKey, $options = [])
 * @method \App\Model\Entity\Organization newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Organization[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Organization|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Organization|bool saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Organization patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Organization[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Organization findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class OrganizationsTable extends Table
{
    // @codeCoverageIgnoreStart
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('organizations');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id'
        ]);
        $this->hasMany('UsersRoles', [
            'foreignKey' => 'organization_id'
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmpty('id', 'create');

        $validator
            ->scalar('label')
            ->maxLength('label', 255)
            ->allowEmpty('label');

        $validator
            ->dateTime('deleted')
            ->allowEmpty('deleted');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['user_id'], 'Users'));

        return $rules;
    }
        // @codeCoverageIgnoreEnd

    public function afterSave($event, $organization, $options)
    {
        if ($organization->isNew()) {
            # Create a new users_roles entry for this organization and user
            $this->UsersRoles = TableRegistry::get("UsersRoles");
            $this->Roles = TableRegistry::get("Roles");

            $userRole = $this->UsersRoles->newEntity([
                'role_id' => $this->Roles->findByLabel('Organization Admin')->first()->id,
                'user_id' => $organization->user_id,
                'organization_id' => $organization->id
            ]);

            if (!$this->UsersRoles->save($userRole)) {
                throw new \Exception("Couldn't create Organization");
            }
        }
    }

    public function addUserByEmail($organizationId, $email)
    {
        $this->Users = TableRegistry::get("Users");
        $this->UsersRoles = TableRegistry::get("UsersRoles");
        $this->Roles = TableRegistry::get("Roles");

        # Check if user exists
        $user = $this->Users->findByEmail($email)->first();
        if (!$user) {
            # User does not exist for this email, create it.
            $user = $this->Users->newEntity([
                'email' => $email,
                'name' => $email
            ]);
            if (!$this->Users->save($user)) {
                throw new \Exception("Could not save user");
            }
        }

        # Add user to organization
        $userRole = $this->UsersRoles->newEntity([
            'role_id' => $this->Roles->findByLabel('Organization Invitee')->first()->id,
            'user_id' => $user->id,
            'organization_id' => $organizationId
        ]);

        if (!$this->UsersRoles->save($userRole)) {
            throw Exception("Couldn't create Organization");
        }
    }

    # Accept an invitation to an Organization
    public function respondToInvite($organizationId, $userId, $accept = 1)
    {
        # Check there is a valid invite
        $this->UsersRoles = TableRegistry::get("UsersRoles");
        $this->Roles = TableRegistry::get("Roles");

        $invite = $this->UsersRoles->find('all', [
            'conditions' => [
                'organization_id' => $organizationId,
                'user_id' => $userId,
                'role_id' => $this->Roles->findByLabel('Organization Invitee')->first()->id
            ]
        ])->first();
        if (!$invite) {
            return false;
        }

        if ($accept) {
            # Accept the invite
            $invite->role_id = $this->Roles->findByLabel('Organization Member')->first()->id;
            $this->UsersRoles->save($invite);
            return $invite;
        } else {
            # Delete it
            $this->UsersRoles->delete($invite);
            return true;
        }
    }

    public function isUserAdmin($userId, $organizationId)
    {
        $this->Roles = TableRegistry::get("Roles");
        $this->UsersRoles = TableRegistry::get("UsersRoles");

        $orgAdminRoleId = $this->Roles->findByLabel('Organization Admin')->first()->id;
        $result = $this->UsersRoles->find('all', [
            'conditions' => [
                'user_id' => $userId,
                'role_id' => $orgAdminRoleId,
                'organization_id' => $organizationId
            ]
        ])->first();
        if ($result) {
            return true;
        }
    }

    public function getAdmins($organizationId) {
        $this->Roles = TableRegistry::get("Roles");
        $this->UsersRoles = TableRegistry::get("UsersRoles");

        $orgAdminRoleId = $this->Roles->findByLabel('Organization Admin')->first()->id;
        return $this->UsersRoles->find('all', [
            'conditions' => [
                'role_id' => $orgAdminRoleId,
                'organization_id' => $organizationId
            ]
        ]);
    }

    public function setUserRole($organizationId, $userId, $roleId)
    {
        $this->UsersRoles = TableRegistry::get("UsersRoles");
        $this->Roles = TableRegistry::get("Roles");

        $usersRole = $this->UsersRoles->find('all', [
            'conditions' => [
                'organization_id' => $organizationId,
                'user_id' => $userId
            ]
        ])->first();

        if (!$usersRole) {
            return false;
        }

        $orgAdminRoleId = $this->Roles->findByLabel('Organization Admin')->first()->id;
        # If the user is not being set to an admin, they are being downgraded to a member.
        # Let's be sure they are not the last admin before doing so.
        if ($roleId != $orgAdminRoleId) {
            $adminCount = $this->getAdmins($organizationId)->count();
            if ($adminCount < 2) {
                throw new \Exception("Organizations must have at least one admin. Cannot remove the last admin.");
            }
        }

        $usersRole->role_id = $roleId;
        $this->UsersRoles->save($usersRole);
        return $usersRole;
    }
}
