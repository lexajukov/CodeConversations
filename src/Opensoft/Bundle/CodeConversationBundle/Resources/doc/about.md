Overview
========

Code Conversations is a deployable PHP Symfony2 application that allows for code browsing, discussions and pull requests
of git repositories for small teams.  It was built in response to a need within a corporation to have better code review
systems modeled after the github concept of a "pull request" without some of the extra overhead.

CodeConversations operates by listening to a repository that is usually the blessed repository, or one that everyone
agrees is the authoritative one.  Team members fix bugs and add features by pushing branches to the blessed repository
and asking they be merged into mainline development by the project leads.

Features

	- Code browsing
	- Commit history per branch
	- Comments on commits
	- Pull requests between branches
	- Comment on pull requests
	- Detection of merges for pull requests

Wish List

	- Comment on a specific line of code in a commit or diff
	- User notifications when things happen
	- Detection of who deleted and created branches
	- Support multiple remotes (or per user git remotes)
	- Better synchronization through post-receive git hooks
	- Improve CSS and UI

Code Conversation is designed to be fairly minimalistic and probably will not attempt to tackle the following: (unless
there is huge demand)

	- Git hosting
	- Access controls or permission levels
	- Integration with (insert your favorite bug tracking software here)

Code Conversations is brought to you by [Richard Fullmer](http://github.com/richardfullmer) at Opensoft.  Fork the project
at `git:CodeConversations.git` and improve it.

Installation
=============

Check out the code by calling `$ git clone git:CodeConversations.git`.

Install the vendor dependencies

	$ cd CodeConversations
	$ ./bin/vendors install

Copy the `app/config/parameters.ini.dist` to `app/config/parameters.ini` and modify the database user, password, and
database name to suit your environment

Install the database by running the following commands:

	$ ./app/console doctrine:database:create
	$ ./app/console doctrine:schema:create

Add a symlink in your local web directory to the public directory of CodeConversations

	$  cd /var/www
	$  sudo ln -s /path/to/CodeConversations/web codeconversations

At this point, the code should be live.  Verify this by visiting `http://localhost/codeconversations` in the browser to
ensure that you get a login prompt.

Up until this point, there are no projects being tracked by CodeConversations, let's fix that:

	$  ./app/console opensoft:code:add-project MyProject http://path/to/MyProject.git

The `opensoft:code:add-project` command will set up Code Conversations to allow it to listen to this git repository and
provide functionality for tracking MyProject. Any number of projects can be added. Code Conversations also will need to
set up synchronization for existing projects.  This will allow it to track changes to this repository over time.

Presently, synchronization is done with a single crontab entry:

     * * * * * /path/to/CodeConversations/app/console opensoft:code:sync

This will allow Code Conversations to attempt to synchronize with this all projects it knows about once every minute.  This
will allow it to track commits and branches as they are added to the project.

Usage
=====

Using CodeConversations is simple.

	1.  Register with Code Conversations by visiting the project with your web browser.  If you don't have a user, create
	    a new one by clicking on the "Register here" link on the login page.  After following a few simple instructions
	    about adding your username and password, you'll be logged in in no time.

	2.  Set up your git alias.  Your Git Alias is how CodeConversations maps the things you do in git with your username
	    in CodeConversations.  While setting up a git alias is not required to use the application, better notification
	    services and tracking are enhanced when it is correctly set up.  Visit the "Edit Profile" page to make sure
	    you've added a git alias.

	3.  Comment on some commits, or some existing pull requests.  Code Conversations tracks comments to any commit or
	    pull request inside of a project.  Authors of the commit you comment or, or of pull requests you comment on will
	    be notified that you're commenting.
	    
	4.  Make some pull requests!

Making a Pull Request
=====================

A pull request in a blessed repository is nothing more than a request to the project maintainer that they pull some code
from one of your feature branches into the main line of development.  Begin by creating a new branch of development for
your feature or bug fix.  After you've made a few commits, and the code is ready to be shared with others or merged into
the mainline of development, push your branch up to the blessed repository so that others (and CodeConversations) are
aware of your work.

Open a new pull request by clicking on the 'Open Pull Request' button on your project's pull request page.  The "Create
Pull Request" form will allow you to specify the base and head branches of the pull request.  Basically, the "Head
Branch" is what you want merged, and the "Base Branch" is where you want it to be merged to.  Often, your head branch is
named after the feature your adding in your pull request, like `feature-make-more-awesome`, and the base branch is where
you want it merged... usually into the project's master or develop branch (for you git-flow gurus out there).

The Pull Request title and description fields are non optional, and give you a way to describe to others on the project
what your pull request is all about, and what it does.  Once you're happy with what you want to say, click the "Make it
so" button and you'll be done.

Once your pull request is created, it'll be listed on the "Active Pull Requests" tab for the project, and for everyone
to see.  Your project maintainer, and other project members are now free to see your commits, the entire diff, and
comments by other team members.  This is the best opportunity for code review and discussion.  Care should be taken that
the project leaders are comfortable with the code which should be merged, that it follow coding guidelines, have unit
tests, and follow the principles outlined by project leaders.  More code can be added to the pull request, like fixing
code style, bugs, or by merging with the base branch changes at any time, and they'll be added and tracked by
CodeConversations.

Once project leads are satisfied with the pull request, they'll check out the code themselves, ensure that tests pass
and nothing major is broken, then merge this code into its destined location.  Merges should be performed with the --no-ff
flag.

Once CodeConversations detects the merge, the pull request will be closed.

