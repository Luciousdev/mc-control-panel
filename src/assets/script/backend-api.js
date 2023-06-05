const express = require("express");
const { spawn } = require("child_process");

const app = express();

app.get("/run-script/:variable", (req, res) => {
    const { variable } = req.params;

    const scriptProcess = spawn("sh", ["/home/luciousdev/" + variable + "/start.sh"], {
        cwd: "/home/luciousdev/" + variable
    });

    scriptProcess.on("close", (code) => {
        console.log(`Script execution completed with code ${code}`);

        if (code === 0) {
            res.send("Script executed successfully");
        } else {
            console.error("Error executing script");
        }
    });

    scriptProcess.on("error", (err) => {
        console.error(`Error executing script: ${err}`);
    });

    let responseSent = false;

    function sendResponse(responseMessage) {
        if (!responseSent) {
            responseSent = true;
            res.send(responseMessage);
        }
    }

    // Handle errors
    function handleError(errorMessage) {
        if (!responseSent) {
            responseSent = true;
            console.error(errorMessage);
            res.status(500).send(errorMessage);
        }
    }

    // Handle script process errors
    scriptProcess.on("error", (err) => {
        handleError(`Error executing script: ${err}`);
    });

    // Handle script process exit with non-zero code
    scriptProcess.on("exit", (code) => {
        if (code !== 0) {
            handleError("Error executing script");
        }
    });
});

app.listen(3000, () => {
    console.log("Server is running on port 3000");
});