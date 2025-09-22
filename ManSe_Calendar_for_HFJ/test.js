// test.js
const { exec } = require("child_process");

function runPhp(action, ...args) {
  return new Promise((resolve, reject) => {
    const cmd = `php ../Lunar-master/cli.php ${action} ${args.join(" ")}`;
    exec(cmd, (err, stdout) => {
      if (err) return reject(err);
      try {
        resolve(JSON.parse(stdout));
      } catch (e) {
        reject(e);
      }
    });
  });
}

(async () => {
  try {
    const lunar = await runPhp("solarToLunar", 2025, 9, 1);
    console.log("→ solarToLunar:", lunar);

    const solar = await runPhp("lunarToSolar", lunar.ly, lunar.lm, lunar.ld, lunar.leap);
    console.log("→ lunarToSolar:", solar);
  } catch (err) {
    console.error("Error:", err);
  }
})();
