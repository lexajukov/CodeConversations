Code Conversations
==================

Code Conversations is a deployable PHP Symfony2 application that allows for code browsing, discussions and pull requests of git repositories for small teams.  It was built in response to a need within a corporation to have better code review systems modeled after the github concept of a "pull request" without some of the extra overhead.

CodeConversations operates by listening to a repository that is usually the blessed repository, or one that everyone agrees is the authoritative one.  Team members fix bugs and add features by pushing branches to the blessed repository and asking they be merged into mainline development by the project leads.

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
	- Better synchronization through post-commit hooks
	
Code Conversation is designed to be fairly minimalistic and probably will not attempt to tackle the following: (unless there is huge demand)

	- Git hosting
	- Access controls or permission levels
	- Integration with (insert your favorite bug tracking software here)
	
Code Conversations is brought to you by Richard Fullmer(http://github.com/richardfullmer) at Opensoft.
	
Installation
=============

Check out the code by calling `$ git clone git:CodeConversations.git`.

Install the vendor dependencies 

	$ cd CodeConversations
	$ ./bin/vendors install
	
Copy the `app/config/parameters.ini.dist` to `app/config/parameters.ini` and modify the database user, password, and database name to suit your environment

Install the database by running the following commands:

	$ ./app/console doctrine:database:create
	$ ./app/console doctrine:schema:create
	
Add a symlink in your local web directory to the public directory of CodeConversations

	$  cd /var/www
	$  sudo ln -s /path/to/CodeConversations/public codeconversations
	
At this point, the code should be live.  Verify this by visiting http://localhost/codeconversations in the browser to ensure that you get a login prompt.

Up until this point, there are no projects being tracked by CodeConversations, let's fix that:

	$  ./app/console opensoft:code:add-project MyProject http://path/to/MyProject.git
	
The `opensoft:code:add-project` command will set up CodeConversations to allow it to listen to this git repository and provide functionality for tracking MyProject. Any number of projects can be added. CodeConversations also will need to set up synchronization for existing projects.  This will allow it to track changes to this repository over time.  

Presently, synchronization is done with a single crontab entry:

     * * * * * /path/to/CodeConversations/app/console opensoft:code:sync

This will allow CodeConversations to attempt to synchronize with this all projects it knows about once every minute.  This will allow it to track commits and branches as they are added to the project.

TODO - Add a git post commit hook which calls opensoft:code:sync directly...

Documentation
=============

Additional documentation can be found on the `about` page of the web interface or located in:

    src/Opensoft/Bundle/CodeConversationsBundle/Resources/doc