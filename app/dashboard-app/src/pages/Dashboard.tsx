import React from 'react';
import Calendar from '../components/Calendar';
import Graphs from '../components/Graphs';
import ProductsTotal from '../components/ProductsTotal';
import ServicesTotal from '../components/ServicesTotal';
import BookingsTotal from '../components/BookingsTotal';
import SalesTotal from '../components/SalesTotal';
import { useData } from '../data';

const Dashboard: React.FC = () => {
    const { products, services, bookings, sales } = useData();

    return (
        <div>
            <h1>Dashboard</h1>
            <ProductsTotal products={products} />
            <ServicesTotal services={services} />
            <BookingsTotal bookings={bookings} />
            <SalesTotal sales={sales} />
            <Graphs data={{ products, services, bookings, sales }} />
            <Calendar bookings={bookings} />
        </div>
    );
};

export default Dashboard;