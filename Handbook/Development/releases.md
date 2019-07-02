# Release Flow

Our release flow is to create a RC (Release Candidate) release, test it. If anything needs changed, we do so, it is Merged, then we create a new RC.

Once an RC is ready for release we mark it as the official versioned release. This can then be pushed to clients based on their onsite support scheduling needs.

## Marking a Release Candidate

Create a new issue named `v1.1 Release Candidate 1` where 1.1 is the version number you are generating a release for.

Click `Create merge request`. This will create a branch for the release steps to be executed in.

### Updating the Changelog

Simply run `growctl changelog 1.1` .

This will compile all the files in Server/Changelogs/ into the top of Server/CHANGELOG.md, and delete the Server/Changelogs/ files.

Now you can commit the Server/ folder with a commit message of `Release 1.1`.

### Creating a Release Candidate Tag

Once the CHANGELOG.md has been updated, you can tag a new release by creating a new tag called `v1.1.RC.1`

## Marking a Release Candidate for Release

Once things have been thoroughly tested and all stakeholders have approved the release, you can create a new tag from the RC tag without the RC part. `v1.1.RC.1` becomes `v1.1`

For the body of this tag, paste in the new section of the CHANGELOG.md file.

Now we have a static set of versioned container images that can be released to onsite and cloud servers.

## Deploying a Release

To release a new version to a specific client or set of servers, just update the `version:` field in the relevant DevOps/Ansible/host_vars/ or group_vars/ file and run an Ansible deploy as described in the [DevOps docs](devops.md).

Start with the test servers `ansible-playbook -i test_hosts onsite_deploy.yml`

Verify everything is good on the tests servers, deployed and working as expected.