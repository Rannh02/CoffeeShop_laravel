#!/usr/bin/env node
/**
 * Lightweight shim for Render's Node start behavior.
 * When Render expects to run `node server.js` (or cannot detect PHP service),
 * this script spawns the PHP built-in server so the Laravel app is served.
 *
 * It reads the $PORT environment variable (Render provides it) and forwards
 * stdout/stderr so logs appear in Render.
 */
const { spawn } = require('child_process');

const port = process.env.PORT || '10000';
const phpArgs = ['-S', `0.0.0.0:${port}`, '-t', 'public'];

console.log(`Starting PHP built-in server on 0.0.0.0:${port}`);

const php = spawn('php', phpArgs, { stdio: 'inherit' });

php.on('exit', (code, signal) => {
  if (signal) {
    console.log(`PHP server terminated with signal ${signal}`);
    process.exit(0);
  }
  console.log(`PHP server exited with code ${code}`);
  process.exit(code);
});

function shutdown() {
  try {
    if (!php.killed) php.kill('SIGTERM');
  } catch (e) {
    // ignore
  }
}

process.on('SIGINT', shutdown);
process.on('SIGTERM', shutdown);
