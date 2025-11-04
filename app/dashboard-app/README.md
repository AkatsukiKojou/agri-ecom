# Dashboard Application

This project is a dashboard application that calculates and displays totals for products, services, bookings, and sales. It includes a calendar interface for managing bookings and visualizes data through various graphs.

## Features

- **Total Products**: Displays the total number of products available.
- **Total Services**: Shows the total number of services offered.
- **Total Bookings**: Calculates and displays the total number of bookings made.
- **Total Sales**: Summarizes the total sales amount.
- **Graphs**: Visual representations of data related to products, services, bookings, and sales.
- **Calendar**: An interactive calendar to manage and view booking dates.

## Project Structure

```
dashboard-app
├── src
│   ├── components
│   │   ├── Calendar.tsx
│   │   ├── Graphs.tsx
│   │   ├── ProductsTotal.tsx
│   │   ├── ServicesTotal.tsx
│   │   ├── BookingsTotal.tsx
│   │   └── SalesTotal.tsx
│   ├── pages
│   │   └── Dashboard.tsx
│   ├── data
│   │   └── index.ts
│   ├── utils
│   │   └── calculations.ts
│   └── types
│       └── index.ts
├── package.json
├── tsconfig.json
└── README.md
```

## Installation

1. Clone the repository:
   ```
   git clone <repository-url>
   ```
2. Navigate to the project directory:
   ```
   cd dashboard-app
   ```
3. Install the dependencies:
   ```
   npm install
   ```

## Usage

To start the application, run:
```
npm start
```

Open your browser and go to `http://localhost:3000` to view the dashboard.

## Contributing

Contributions are welcome! Please open an issue or submit a pull request for any enhancements or bug fixes.

## License

This project is licensed under the MIT License.