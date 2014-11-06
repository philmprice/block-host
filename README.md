Block: Host
====================
This is the head/main package for the 'Blocks' system. It bootstraps all other blocks around, auto-scaffolds extended assets (in dev mode), and routes HTTP traffic among them.

### About the Blocks system
The Blocks system is a way to package, distribute, and deploy web application modules that:
- Deploy and pull-dependancies with Composer
- Bootstrap into an MVC Framework (PhalconPHP at time of writing)
- Keep core code separate from extended project code

In this way, all Blocks (even the host):
- Are extendable
- Bring their dependencies along
- May be upgraded by replacing core

It is currently in development, more info to come as things progress.
