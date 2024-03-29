const fs = require('fs');
const csv = require('csv-parser');

const results = [];
const seenValues = new Set(); // Use a Set to keep track of seen values

fs.createReadStream('./camera-logs-28-03-2024-bkp.csv')
    .pipe(csv())
    .on('data', (data) => {
        const key = `${data.UserCode}-${data.DeviceID}-${data.RecordDate}`; // Create a unique key based on the fields you want to check for duplicates
        if (!seenValues.has(key)) {
            seenValues.add(key); // Add the key to the Set
            results.push(data); // Push the data if it's not a duplicate
        }
    })
    .on('end', () => {
        console.log(results);
        // Process the parsed CSV data without duplicates here
    });
