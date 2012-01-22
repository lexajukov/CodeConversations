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

