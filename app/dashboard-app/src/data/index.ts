export const mockData = {
  products: [
    { id: 1, name: "Product A", price: 10 },
    { id: 2, name: "Product B", price: 15 },
    { id: 3, name: "Product C", price: 20 },
  ],
  services: [
    { id: 1, name: "Service A", price: 30 },
    { id: 2, name: "Service B", price: 45 },
  ],
  bookings: [
    { id: 1, date: "2023-10-01", serviceId: 1 },
    { id: 2, date: "2023-10-02", serviceId: 2 },
  ],
  sales: [
    { id: 1, productId: 1, amount: 100 },
    { id: 2, productId: 2, amount: 150 },
    { id: 3, productId: 3, amount: 200 },
  ],
};