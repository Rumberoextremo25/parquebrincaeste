import TitleSection from "@/Components/TitleSection";
import { GlobeAmericasIcon, UserGroupIcon } from "@heroicons/react/24/outline";
import React from "react";

const birthday = () => {
    return (
        <div className="py-section container">
            <div className="grid gap-24 lg:grid-cols-2">
                <div>
                    <TitleSection title="Plan Básico" subTitle="Paquetes de Fiestas" />
                    <div className="mt-4 space-y-6 ">
                        <p className="text">
                            * 1 Cilindro 
                        </p>
                        <p className="mt-4 space-y-6">
                            * 1 paraban personalizado con el motivo de la fiesta (puede escoger entre luna o círculo).
                        </p>
                        <p className="mt-4 space-y-6">
                            * 150 globos y una alfombra o grama de base.
                        </p>
                        <p className="mt-4 space-y-6">
                            * Cumpleañero GRATIS.
                        </p>
                        <p className="mt-4 space-y-6">
                            * 10 comidas incluidas de cada concesión del parque.
                        </p>
                    </div>
                </div>
                <div className="hidden lg:block">
                    <img
                        src="/img/about/plan basico.webp"
                        alt=""
                        className="object-lefts h-full object-cover"
                    />
                </div>
            </div>
            <br></br>
            <div className="grid gap-24 lg:grid-cols-2">
            <div className="hidden lg:block">
                    <img
                        src="/img/about/plan medio.jpeg"
                        alt=""
                        className="object-lefts h-full object-cover"
                    />
                </div>
                <div>
                    <TitleSection title="Plan Medio" subTitle="" />
                    <div className="mt-4 space-y-6 ">
                        <p className="text">
                            * 3 cilindros (1 con motivo y 2 de colores). 
                        </p>
                        <p className="mt-4 space-y-6">
                            * 1 paraban personalizado con el motivo de la fiesta (que se escoge entre luna o vinil).
                        </p>
                        <p className="mt-4 space-y-6">
                            * 200 globos.
                        </p>
                        <p className="mt-4 space-y-6">
                            * Una alfombra o grama de base.
                        </p>
                        <p className="mt-4 space-y-6">
                            * 15 brazaletes más 15 medias antideslizantes. Válido para niños.
                        </p>
                        <p className="mt-4 space-y-6">
                            * 15 comidas de cada concesión del parque.
                        </p>
                    </div>
                </div>
            </div>
            <br></br>
            <div className="grid gap-24 lg:grid-cols-2">
                <div>
                    <TitleSection title="Plan VIP" subTitle="" />
                    <div className="mt-4 space-y-6 ">
                        <p className="text">
                            * 3 cilindros + PISO ROTULADO.
                        </p>
                        <p className="mt-4 space-y-6">
                            * 1 paraban personalizado + nombre del cumpleañero de 3 metros cuadrado.
                        </p>
                        <p className="mt-4 space-y-6">
                            * 400 globos (normales y cromados).
                        </p>
                        <p className="mt-4 space-y-6">
                            * 1 letra, figura o número tamaño 85cm de altura.
                        </p>
                        <p className="mt-4 space-y-6">
                            * 25 brazaletes más 25 medias antideslizantes.
                        </p>
                        <p className="mt-4 space-y-6">
                            * 25 comidas incluidas de cada concesión del parque.
                        </p>
                    </div>
                </div>
                <div className="hidden lg:block">
                    <img
                        src="/img/about/Plan VIP.webp"
                        alt=""
                        className="object-lefts h-full object-cover"
                    />
                </div>
            </div>
        </div>
    );
};

export default birthday;