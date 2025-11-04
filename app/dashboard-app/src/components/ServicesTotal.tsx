import React from 'react';

interface ServicesTotalProps {
    servicesData: Array<{ id: number; name: string; price: number }>;
}

const ServicesTotal: React.FC<ServicesTotalProps> = ({ servicesData }) => {
    const totalServices = servicesData.length;

    return (
        <div>
            <h2>Total Services</h2>
            <p>{totalServices}</p>
        </div>
    );
};

export default ServicesTotal;