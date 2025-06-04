#!/usr/bin/env node

const { Command } = require('commander');
const program = new Command();

program
  .name('commander-cli')
  .description('A simple CLI scaffold with commander.js')
  .version('1.0.0');

program
  .command('hello')
  .description('Print Hello, World!')
  .action(() => {
    console.log('Hello, World!');
  });

program.parse(process.argv);
