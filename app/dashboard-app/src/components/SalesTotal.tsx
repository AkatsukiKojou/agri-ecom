import React from 'react';

interface SalesTotalProps {
    salesData: number[];
}

const SalesTotal: React.FC<SalesTotalProps> = ({ salesData }) => {
    const totalSales = salesData.reduce((acc, curr) => acc + curr, 0);

    return (
        <div>
            <h2>Total Sales</h2>
            <p>${totalSales.toFixed(2)}</p>
        </div>
    );
};

export default SalesTotal;