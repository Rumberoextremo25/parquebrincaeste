import React from 'react'
import FrequentlyAsked from "@/Components/FrequentlyAsked";
import Layout from "@/Layouts/Layout";
import BannerHero from '@/Components/Hero/BannerHero';

const Stand = () => {
    return (
        <Layout>
            <BannerHero title="FERIA DE COMIDA" />
            <div className="py-section container">
            <div className="grid gap-24 lg:grid-cols-2">
                <div>
                    <strong><h5>BRINCA MANIA</h5></strong>
                    <p className="mt-4 space-y-6 ">
                        Descubre los sabores de Brinca Manía, donde cada bocado es una aventura. Desde pizzas y perros calientes hasta cotufas y helados, pasando por donas, algodón de azúcar, nuggets, tequeños y papas fritas. ¡Ven y disfruta de nuestras delicias culinarias!
                    </p>
                    <p className='mt-4 space-y-6'>
                        Tambien endulza tu día en el paraíso de las golosinas. Descubre nuestra selección de chucherías, gomitas, y bolsas de papitas crujientes. ¡Un festín de sabores y diversión te espera en cada visita!
                    </p>
                </div>
                <div className="hidden lg:block">
                    <img
                        src="/img/stand/IMG_2756.jpg"
                        alt=""
                        className="object-lefts h-full object-cover"
                    />
                </div>
            </div>
            <br></br>
            <div className="grid gap-24 lg:grid-cols-2">
            <div className="hidden lg:block">
                    <img
                        src="/img/stand/Fantasy.jpg"
                        alt=""
                        className="object-lefts h-full object-cover"
                    />
                </div>
                <div>
                <strong><h5>DISNEY FANTASY</h5></strong>
                    <div className="mt-4 space-y-6 ">
                        <p className="text">
                            Vive la magia de disney con los increibles bolsos y morrales del maravilloso mundo de disney!. 
                        </p>
                    </div>
                </div>
            </div>
            <br></br>
            <div className="grid gap-24 lg:grid-cols-2">
                <div>
                <strong><h5>DUTCH PANCAKES</h5></strong>
                    <div className="mt-4 space-y-6 ">
                        <p className="text">
                            * Deléitate en Dutch Pancakes con nuestros irresistibles pancakes, perfectos para cualquier momento del día. Acompáñalos con nuestros aromáticos cafés o refrescantes smoothies..
                        </p>
                    </div>
                </div>
                <div className="hidden lg:block">
                    <img
                        src="/img/stand/IMG_2751.jpg"
                        alt=""
                        className="object-lefts h-full object-cover"
                    />
                </div>
            </div>
            <br></br>
            <div className="grid gap-24 lg:grid-cols-2">
            <div className="hidden lg:block">
                    <img
                        src="/img/stand/Proximamente.png"
                        alt=""
                        className="object-lefts h-full object-cover"
                    />
                </div>
                <div>
                <strong><h5>Brinca Burguer</h5></strong>
                    <div className="mt-4 space-y-6 ">
                        <p className="text">
                            *  PROXIMAMENTE !!. 
                        </p>
                    </div>
                </div>
            </div>
            <br></br>
        </div>
        </Layout>
    )
}

export default Stand