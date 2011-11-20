UPGRADING
=========

 * 0.0.2-DEV

   * [Less](http://lesscss.org/) is now used to manage styles and stylesheets.  Compiling less into CSS requires the use
     of nodejs and the less module.

     * NodeJS Installation: [Installation](https://github.com/joyent/node/wiki/Installation) >= 4.0
     * npm (node package manager): `curl http://npmjs.org/install.sh > node_install.sh && sudo sh node_install.sh`
     * less module: `sudo npm install less -g`

     Configuration of the node assetic filter might be required in `app/config/config.yml`.  The node binary and path to
     modules should be under the assetic -> filters -> less category.

   * Assetic now manages all assets.  During each deployment, the following must be run to ensure that Code Conversations
     has the latest versions of provided assets.

         ./app/console -e=prod --no-debug assetic:dump

   * Email notification services were added to Code Conversations.  Configure this service by ensuring that the correct
     parameters exist for sending emails in `app\config\parameters.ini`.