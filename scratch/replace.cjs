const fs = require('fs');

const targetPath = 'e:/CODE/DEV/du_an/resources/js/Pages/Positions/Index.vue';
const templatePath = 'e:/CODE/DEV/du_an/scratch/new_template.vue';

const targetContent = fs.readFileSync(targetPath, 'utf8');
const templateContent = fs.readFileSync(templatePath, 'utf8');

const targetLines = targetContent.split(/\r?\n/);

let startIdx = -1;
for (let i = 0; i < targetLines.length; i++) {
    if (targetLines[i].trim() === '<template>') {
        startIdx = i;
        break;
    }
}

let endIdx = -1;
for (let i = targetLines.length - 1; i >= 0; i--) {
    if (targetLines[i].trim() === '</template>') {
        endIdx = i;
        break;
    }
}

if (startIdx !== -1 && endIdx !== -1) {
    const beforeLines = targetLines.slice(0, startIdx);
    const afterLines = targetLines.slice(endIdx + 1);
    
    const result = beforeLines.join('\n') + '\n' + templateContent + '\n' + afterLines.join('\n');
    
    fs.writeFileSync(targetPath, result, 'utf8');
    console.log('Successfully replaced template block.');
} else {
    console.log('Failed to find template tags');
    process.exit(1);
}
