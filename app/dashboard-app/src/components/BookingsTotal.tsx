import React from 'react';

interface BookingsTotalProps {
    bookingsData: Array<{ id: number; date: string; customer: string }>;
}

const BookingsTotal: React.FC<BookingsTotalProps> = ({ bookingsData }) => {
    const totalBookings = bookingsData.length;

    return (
        <div>
            <h2>Total Bookings</h2>
            <p>{totalBookings}</p>
        </div>
    );
};

export default BookingsTotal;