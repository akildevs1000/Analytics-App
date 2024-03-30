const fs = require('fs');
const path = require('path');


const seenValues = new Set(); // Use a Set to keep track of seen values
const predefinedIDs = ["5656", "7890", "1234", "9876","1111", "2222", "3333"]; // Add your predefined IDs here



// Function to generate random data for each entry
function generateRandomData() {
    const UserCode = predefinedIDs[Math.floor(Math.random() * predefinedIDs.length)];
    const DeviceID = Math.random() < 0.5 ? '1111111111' : '2222222222';
    const RecordDate = new Date().toISOString().slice(0, 19).replace('T', ' ');
    const RecordNumber = Math.floor(Math.random() * 100) + 1;
    const FaceID = Math.floor(Math.random() * 200) + 1;
    const Clarity = Math.random().toFixed(6);
    const Age = Math.floor(Math.random() * 100) + 1;
    const Quality = Math.random().toFixed(6);
    const Gender = Math.random() < 0.5 ? 'Male' : 'Female';
    const Similarity = Math.random().toFixed(6);

    return `${UserCode},${DeviceID},${RecordDate},${RecordNumber},${FaceID},${Clarity},${Age},${Quality},${Gender},${Similarity}\n`;
}

// Function to create CSV file with random entries
function createRandomCSV(filename) {
    let log = generateRandomData();

    if (!seenValues.has(log)) {
        seenValues.add(log); // Add the key to the Set
        fs.appendFileSync(filename, log);
        console.log(`Data was written to ${filename}`);
    }
}

// Function to call createRandomCSV every 10 seconds
function generateDataEvery10Seconds(filename) {
    createRandomCSV(filename);
    setTimeout(() => {
        generateDataEvery10Seconds(filename);
    }, 30000); // 10 seconds in milliseconds
}


function getFormattedDate() {
    const options = {
        year: "numeric",
        month: "2-digit",
        day: "2-digit",
        hour: "2-digit",
        minute: "2-digit",
        second: "2-digit",
        hour12: false, // Use 24-hour format
        timeZone: "Asia/Dubai",
    };

    const [newDate, newTime] = new Intl.DateTimeFormat("en-US", options)
        .format(new Date())
        .split(",");
    const [m, d, y] = newDate.split("/");
    const formattedDate = `${d.padStart(2, 0)}-${m.padStart(2, 0)}-${y}`;

    let formattedTime = newTime.replace(/\s/g, '');


    return {
        formattedDate, formattedTime
    };
}


let { formattedDate, formattedTime } = getFormattedDate();

let filename = `../web-app/backend/storage/app/camera/camera-logs-${formattedDate}.csv`;

const logFileDirPath = path.dirname(filename);
if (!fs.existsSync(logFileDirPath)) {
    fs.mkdirSync(logFileDirPath, { recursive: true });
}

generateDataEvery10Seconds(filename);
