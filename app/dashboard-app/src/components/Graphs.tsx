import React from 'react';
import { Bar } from 'react-chartjs-2';

interface GraphsProps {
  productsTotal: number;
  servicesTotal: number;
  bookingsTotal: number;
  salesTotal: number;
}

const Graphs: React.FC<GraphsProps> = ({ productsTotal, servicesTotal, bookingsTotal, salesTotal }) => {
  const data = {
    labels: ['Products', 'Services', 'Bookings', 'Sales'],
    datasets: [
      {
        label: 'Total Counts',
        data: [productsTotal, servicesTotal, bookingsTotal, salesTotal],
        backgroundColor: [
          'rgba(75, 192, 192, 0.6)',
          'rgba(153, 102, 255, 0.6)',
          'rgba(255, 159, 64, 0.6)',
          'rgba(255, 99, 132, 0.6)',
        ],
        borderColor: [
          'rgba(75, 192, 192, 1)',
          'rgba(153, 102, 255, 1)',
          'rgba(255, 159, 64, 1)',
          'rgba(255, 99, 132, 1)',
        ],
        borderWidth: 1,
      },
    ],
  };

  return (
    <div>
      <h2>Total Overview</h2>
      <Bar data={data} />
    </div>
  );
};

export default Graphs;