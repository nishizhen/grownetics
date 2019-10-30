# Development at Grownetics

## Development Methodologies

### Versioning

We use [Semantic Versioning 2.0](http://semver.org/) for versioning all projects.

## How We Use Git / GitLab

### Doing Work

Master is always the latest deployable version. While we do do versioned releases, we never want master itself to be in a state where it is broken.

All work starts with an issue. If there is not an issue for the change that needs made, create one using an Issue Template. You can create issues several ways, through the [GitLab New Issue page](https://code.cropcircle.io/Grownetics/Grownetics/issues/new), by pressing the `i` key anywhere on our GitLab instance, or by using our Mattermost integration `/gitlab issue new Improve the documentation around issue creation`

Once there is an issue, click the green `Create Merge Request` button. This will make a new branch for you tied to a MR (Merge Request). Do all your work on this branch. This leaves you free to jump on other unrelated issues as needed if anything urgent comes up.

When you have finished with your changes, push your code to git. Once the pipeline succeeds for your MR, you can deploy a Review instance of the GrowServer, the Handbook, or both, by clicking the gray circles on the far right next to the Pipeline status. Once the Review app deploy has completed, the MR will be updated with a clickable link that will take you to the Review app. Review your changes in the review app and make sure they work as expected with no issues. Perform any manual testing steps that were added to the [Testing](testing.md) document.

Once you are sure everything is well tested and production ready, add the `Ready for Review` label. In the next Code Review meeting, we will review, approve, and merge the changes, or comment on them and remove the `Ready for Review` label as appropriate.

### Reviewing Work

When a MR is assigned to you, click through to the issue referenced, make sure you understand what the issue is about, and why it was needed to begin with. Now return to the MR and click the 'Changes' tab. If it's a lot, or you don't fully understand everything right away, it may be worth a voice or video call with whoever submitted the MR. Ask questions, walk through the changes, and make sure things look good. Open the review app and make sure things do actually work as they are supposed to. Now you can 'Accept' the MR. If at any point in your review you found something that should be changed, simply comment what is wrong and needs fixed, and remove the `Ready for Review` label. The submitter will address any issues and re-add the label.

Reference: https://gitlab.com/gitlab-org/gitlab-ce/blob/462a4b793c0110130f101cd66bf84b8aac2909b6/doc/workflow/gitlab_flow.md

Don't forget to set user.name and user.email to your grownetics email with 'git config'

## Developer Setup

### [OS X Dev Environment Setup](dev-osx.md)

### [PHPStorm Xdebug Remote Debugging Setup](dev-xdebug.md)

### Power Save Mode
If you're developing on your laptop and you're too far away from a power outlet, check out PhpStorm's 'File->Power Save Mode' option.

## Developing locally

*  Install [Docker](http://docker.com).
*  Install [Go](https://golang.org/dl/). Make sure to set your GOPATH to your local Grownetics/ repo folder.
*  Create a cache directory inside of Server/html/tmp, run `mkdir -p Server/html/tmp/cache`
*  You'll need to login to the new registry server `> $ docker login code.cropcircle.io:4567` with your username and an Access Token named docker you can create in GitLab under Settings.
*  Set your GOPATH `export GOPATH=$GOPATH:~/Code/Grownetics`
*  Run `go get code.cropcircle.io/grownetics/...` then `go install code.cropcircle.io/grownetics/...`
*  Now running `growctl` anywhere should show you the commands available. `growctl up` will get you a fully functional local dev environment.
*  Once `growctl up` has completed run `docker exec -it growserver_growdash_1 bash` and inside the shell run `composer install` to install composer dependencies
*  Following dependancy installation exit container shell and run `growctl seed`
*  Install bower on your machine `npm install -g bower` https://bower.io/
*  Inside of your Server/html directory run `bower install`

## Configure Docker with PHPStorm

Add a new 'Docker Deployment' Run/Debug Configuration called 'Onsite'. For 'Server' select a new Docker server with docker-compose path set to `/usr/local/bin/docker-compose` and make sure that 'Certificates folder' is empty. For 'Deployment' select `Server/docker-compose.yml`. Now open View -> Tool Windows -> Docker. Click the green multi-arrow icon on the left, and select the 'Onsite' configuration you created earlier. Watch in amazement as PHPStorm spins up all the images you need.

## Working with Docker

We deploy and manage our stack using Docker. Our docker-compose files in this diretory are responsible for spinning up the stack in various environments and configurations.

### Building docker base images

```
docker build Server/html --no-cache -t grownetics/build:1.0 -f Server/html/images/build/Dockerfile
```
