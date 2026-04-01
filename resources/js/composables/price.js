export function calculateTotalVAT(details = []) {
    return details.reduce((total, item) => {
        const quantity = Number(item.quantity) || 0;
        const price = Number(item.price) || 0;
        const vatRate = Number(item.vat) || 0;
        const vatAmount = quantity * price * (vatRate / 100);
        return total + vatAmount;
    }, 0);
}


export function calculateTotalOrderNoVat(details = []) {
    return details.reduce((total, item) => {
        const quantity = Number(item.quantity) || 0;
        const price = Number(item.price) || 0;
        const discount = Number(item.discount) || 0;
        return total + ((quantity * price) - discount)
    }, 0);
}