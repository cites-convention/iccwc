/**
 * @file
 * Compile critical CSS.
 */

'use strict';

const critical = require('critical');
var prepend = require('prepend');
var fs = require('fs');

const filename = 'css/critical/15.css';
const args = process.argv.slice(2);
const url = args[0];
if (!url) {
  console.error('Please specify the local site URL as a parameter. Usage: node critical.js http://site.local');
  return;
}

fs.unlinkSync(filename);
critical.generate({
  base: '.',
  src: url,
  target: 'css/critical/15.css',
  width: 1300,
  height: 800,
})

prepend(filename, "@import url('https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,400;0,700;1,400;1,700&family=Source+Sans+Pro:wght@400;700&display=swap');", function(error) {
  if (error)
    console.error(error.message);
});
