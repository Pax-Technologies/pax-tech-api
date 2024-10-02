const puppeteer = require('puppeteer');

// Récupère les arguments de la ligne de commande
const invoiceId = process.argv[2]; // ID de la facture
const baseUrl = process.argv[3]; // URL de base

(async () => {
    // Lance une instance de Chromium
    const browser = await puppeteer.launch();
    const page = await browser.newPage();

    // Définit la taille de la vue
    await page.setViewport({
        width: 1250,
        height: 1300,
    });

    // Intercepte les requêtes et affiche les URLs dans la console
    await page.setRequestInterception(true);
    page.on("request", request => {
        console.log(`Request URL: ${request.url()}`); // Affiche l'URL de la requête
        request.continue();
    });

    // Affiche l'URL que l'on va charger
    const invoiceUrl = `${baseUrl}/invoice_show/${invoiceId}`;
    console.log(`Navigating to: ${invoiceUrl}`);

    // Charge l'URL de la page HTML que tu veux transformer en PDF
    await page.goto(invoiceUrl, { waitUntil: 'networkidle2' });

    // Génère le PDF et le sauvegarde
    const pdfPath = `facture_${invoiceId}.pdf`; // Chemin où le PDF sera enregistré
    await page.pdf({
        path: pdfPath,
        format: 'A4',
        printBackground: true
    });
    console.log(`PDF generated: ${pdfPath}`);

    await browser.close();
})();
