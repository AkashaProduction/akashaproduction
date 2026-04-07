const http = require("http");
const next = require("next");

const port = parseInt(process.env.PORT || "3000", 10);
const hostname = "127.0.0.1";
const app = next({ dev: false, hostname, port });
const handle = app.getRequestHandler();

app
  .prepare()
  .then(() => {
    http
      .createServer((req, res) => handle(req, res))
      .listen(port, hostname, () => {
        console.log(`Akasha Production listening on ${hostname}:${port}`);
      });
  })
  .catch((error) => {
    console.error("Failed to start Next.js under Passenger:", error);
    process.exit(1);
  });
