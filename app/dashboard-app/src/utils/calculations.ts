export const calculateTotalProducts = (products) => {
    return products.length;
};

export const calculateTotalServices = (services) => {
    return services.length;
};

export const calculateTotalBookings = (bookings) => {
    return bookings.length;
};

export const calculateTotalSales = (sales) => {
    return sales.reduce((total, sale) => total + sale.amount, 0);
};