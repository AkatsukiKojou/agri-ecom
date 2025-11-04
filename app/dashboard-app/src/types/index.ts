export interface Product {
    id: number;
    name: string;
    price: number;
    quantity: number;
}

export interface Service {
    id: number;
    name: string;
    price: number;
}

export interface Booking {
    id: number;
    date: string;
    serviceId: number;
    customerName: string;
}

export interface SalesData {
    totalSales: number;
    totalBookings: number;
    totalProducts: number;
    totalServices: number;
}