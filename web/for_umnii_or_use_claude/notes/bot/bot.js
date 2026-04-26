const crypto = require("node:crypto");
const process = require('node:process');
const child_process = require('node:child_process');

const puppeteer = require("puppeteer");

const readline = require('readline').createInterface({
    input: process.stdin,
    output: process.stdout,
    terminal: false,
});
readline.ask = str => new Promise(resolve => readline.question(str, resolve));

const sleep = ms => new Promise(resolve => setTimeout(resolve, ms));

const TIMEOUT = process.env.TIMEOUT || 180 * 1000;
const TASK_URL = process.env.TASK_URL || 'http://localhost:8002/';

const FLAG = process.env.FLAG || 'flag{dummy}';

const POW_BITS = process.env.POW_BITS || 28;

function makeid(length) {
    let result = '';
    const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    const charactersLength = characters.length;
    let counter = 0;
    while (counter < length) {
      result += characters.charAt(Math.floor(Math.random() * charactersLength));
      counter += 1;
    }
    return result;
}

async function pow() {
    const nonce = crypto.randomBytes(8).toString('hex');

    console.log('[*] Please solve PoW:');
    console.log(`hashcash -q -mb${POW_BITS} ${nonce}`);

    const answer = await readline.ask('> ');

    const check = child_process.spawnSync(
        '/usr/bin/hashcash',
        ['-q', '-f', './hashcash.sdb', `-cdb${POW_BITS}`, '-r', nonce, answer],
    );
    const correct = (check.status === 0);

    if (!correct) {
        console.log('[-] Incorrect.');
        process.exit(0);
    }

    console.log('[+] Correct.');
}

async function visit(url) {
    const params = {
        browser: 'chrome',
        args: [
            '--no-sandbox',
            '--disable-gpu',
            '--disable-extensions',
            '--js-flags=--jitless',
	    '--disable-features=TrackingProtection3pcd'
        ],
        headless: true,
    };

    const browser = await puppeteer.launch(params);

    const pid = browser.process().pid;

    const shutdown = async () => {
        await context.close();
        await browser.close();

        try {
            process.kill(pid, 'SIGKILL');
        } catch(_) { }

        process.exit(0);
    };

    const LOGIN = makeid(16);
    const PASSWORD = makeid(16);

    const page1 = await browser.newPage();
    await page1.goto(`${TASK_URL}register`);

    await page1.waitForSelector('.form-control');
    await page1.type('.form-control#username', LOGIN);
    await page1.type('.form-control#password', PASSWORD);
    await page1.click('.btn[type="submit"]');

    await sleep(300);

    await page1.goto(`${TASK_URL}login`);

    await page1.waitForSelector('.form-control');
    await page1.type('.form-control#username', LOGIN);
    await page1.type('.form-control#password', PASSWORD);
    await page1.click('.btn[type="submit"]');
    await sleep(300);

    await page1.goto(`${TASK_URL}notes/create`);
    await page1.waitForSelector('textarea#content');
    await page1.type('#title', "flag");
    await page1.type('textarea#content', FLAG);
    await page1.click('.btn[type="submit"]');
    await sleep(300);

    // setTimeout(() => shutdown(), TIMEOUT);

    console.log(`[+] Visit ${url}`);
    await page1.goto(url);

    await sleep(TIMEOUT);
    await page1.close();
}

async function main() {
    if (POW_BITS > 0) {
        await pow();
    }

    console.log('[?] Please input URL:');
    const url = await readline.ask('> ');

    if (!url.startsWith("http://") && !url.startsWith("https://")) {
        console.log('[-] Access denied.');
        process.exit(0);
    }

    console.log('[+] OK.');


    await visit(url);

    readline.close()
    process.stdin.end();
    process.stdout.end();
}

main();
