import Card from "@/Components/Card";
import ListDescription from "@/Components/ListDescription";
import React from "react";

const CardsInformation = () => {
    return (
        <div className="flex justify-center items-center">
            <dl className="shadow-lg rounded-lg p-6 bg-white">
                <div className="space-y-4">
                    <ListDescription title="Nombre">
                        <strong>Brinca Este 2024 C.A</strong>
                    </ListDescription>
                    <ListDescription title="Teléfono">
                        <strong>+58 412-3508826</strong>
                    </ListDescription>
                    <ListDescription title="Email">
                        <strong>tickets@parquebrincaeste.com</strong>
                    </ListDescription>
                </div>
            </dl>
        </div>
    );
};

export default CardsInformation;
