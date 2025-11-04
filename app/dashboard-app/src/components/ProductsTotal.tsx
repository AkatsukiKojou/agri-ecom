import React from 'react';

interface ProductsTotalProps {
    products: { id: number; name: string; quantity: number }[];
}

const ProductsTotal: React.FC<ProductsTotalProps> = ({ products }) => {
    const totalProducts = products.reduce((total, product) => total + product.quantity, 0);

    return (
        <div>
            <h2>Total Products</h2>
            <p>{totalProducts}</p>
        </div>
    );
};

export default ProductsTotal;