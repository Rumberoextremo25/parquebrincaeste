import React, { useState, useEffect } from "react";
import { router } from "@inertiajs/react";
import Layout from "@/Layouts/Layout";
import BannerHero from "@/Components/Hero/BannerHero";

const Tienda = (props) => {
    // Inicializa PRODUCTS desde props, asegurando que el precio sea flotante
    const [PRODUCTS, setPRODUCTS] = useState(
        props?.products.map((item) => ({
            ...item,
            price: parseFloat(item.price),
        })) ?? []
    );

    // --- Estados para el formulario de selección ---
    const getDefaultValues = () => {
        const today = new Date();
        return {
            fecha: today.toLocaleDateString("en-CA", {
                // "en-CA" para formato YYYY-MM-DD
                year: "numeric",
                month: "2-digit",
                day: "2-digit",
            }),
        };
    };

    const defaultValue = getDefaultValues();

    const [fecha, setFecha] = useState(defaultValue.fecha);
    const [mensaje, setMensaje] = useState("");

    const [clientType, setClientType] = useState(null); // 'adultOrOver6' o 'under6'
    const [selectedBraceletId, setSelectedBraceletId] = useState("");
    const [braceletQuantity, setBraceletQuantity] = useState(1);

    const [selectedSockTallaId, setSelectedSockTallaId] = useState("");
    const [sockQuantity, setSockQuantity] = useState(1); // Cantidad de calcetines fija en 1

    // --- Estado para el carrito de compras ---
    const [cartItems, setCartItems] = useState([]);

    // --- Productos derivados para facilitar el acceso ---
    // Función para obtener el día de la semana (0 = Domingo, 1 = Lunes, ..., 6 = Sábado)
    const getDayOfWeek = (dateString) => {
        const date = new Date(dateString + "T12:00:00"); // Añade T12:00:00 para evitar problemas de zona horaria
        return date.getDay();
    };

    // Determina si la fecha seleccionada es de fin de semana (Viernes, Sábado, Domingo)
    const isWeekend =
        getDayOfWeek(fecha) === 5 ||
        getDayOfWeek(fecha) === 6 ||
        getDayOfWeek(fecha) === 0; // 5=Viernes, 6=Sábado, 0=Domingo
    // Determina si la fecha seleccionada es de entre semana (Martes, Miércoles, Jueves)
    const isWeekday = getDayOfWeek(fecha) >= 2 && getDayOfWeek(fecha) <= 4; // 2=Martes, 3=Miércoles, 4=Jueves

    // Filtra los brazaletes según el día de la semana
    const filteredBabyParkBraceletProduct = PRODUCTS.find(
        (p) =>
            p.category === "Pass Baby Park" &&
            ((isWeekday && p.name.includes("(Martes a Jueves)")) ||
                (isWeekend && p.name.includes("(Viernes a Domingo)")))
    );

    const filteredHourlyBraceletsProducts = PRODUCTS.filter(
        (p) =>
            p.category === "Brazalete" &&
            ((isWeekday && p.name.includes("(Martes a Jueves)")) ||
                (isWeekend && p.name.includes("(Viernes a Domingo)")))
    );

    // Filtra las medias (calcetines) - su precio ya es fijo en $1.50 desde el seeder
    const socksProducts = PRODUCTS.filter((p) => p.category === "Calcetines");

    const selectedBraceletProduct = PRODUCTS.find(
        (p) => p.id === selectedBraceletId
    );
    const selectedSockProduct = PRODUCTS.find(
        (p) => p.id === selectedSockTallaId
    );

    // --- Handlers de selección de formulario ---
    const handleClientTypeChange = (type) => {
        setClientType(type);
        setSelectedBraceletId("");
        setBraceletQuantity(1);
        setSelectedSockTallaId(""); // Reiniciar talla de medias al cambiar tipo de cliente
        setSockQuantity(1); // Mantener en 1

        // Forzar selección del brazalete Baby Park si el tipo de cliente es 'under6'
        if (type === "under6" && filteredBabyParkBraceletProduct) {
            setSelectedBraceletId(filteredBabyParkBraceletProduct.id);
            setBraceletQuantity(1);
        }
    };

    // MODIFICACIÓN IMPORTANTE: useEffect para forzar la selección de calcetines
    useEffect(() => {
        // Verificar si se ha seleccionado algún brazalete (sea Baby Park o por hora)
        const isAnyBraceletSelected = selectedBraceletId !== "";

        if (isAnyBraceletSelected && socksProducts.length > 0) {
            // Si hay un brazalete seleccionado y hay calcetines disponibles,
            // y no se ha seleccionado una talla de calcetín, o la que se tiene no aplica,
            // forzar la selección del primer calcetín disponible.
            const currentSelectedSockApplies = socksProducts.some(
                (sock) => sock.id === selectedSockTallaId
            );

            if (!selectedSockTallaId || !currentSelectedSockApplies) {
                // Asegúrate de que el ID del calcetín sea un string si así lo esperas en el select
                setSelectedSockTallaId(String(socksProducts[0].id));
            }
            setSockQuantity(1); // Asegurar que la cantidad sea 1
        } else if (!isAnyBraceletSelected) {
            // Si no hay brazalete seleccionado, permitir que el calcetín sea opcional
            // o deseleccionarlo si ya estaba seleccionado
            setSelectedSockTallaId("");
            setSockQuantity(1);
        }
    }, [selectedBraceletId, socksProducts, selectedSockTallaId]);

    // Recalcular los productos cuando la fecha cambie para obtener los precios correctos
    useEffect(() => {
        // Al cambiar la fecha, re-evaluar si el brazalete actualmente seleccionado sigue siendo válido
        // para el nuevo día (entre semana/fin de semana).
        // Si no, deseleccionarlo.
        if (selectedBraceletProduct) {
            const currentProductId = selectedBraceletProduct.id;
            let isValidSelection = false;

            if (selectedBraceletProduct.category === "Pass Baby Park") {
                isValidSelection =
                    filteredBabyParkBraceletProduct &&
                    filteredBabyParkBraceletProduct.id === currentProductId;
            } else if (selectedBraceletProduct.category === "Brazalete") {
                isValidSelection = filteredHourlyBraceletsProducts.some(
                    (p) => p.id === currentProductId
                );
            }

            if (!isValidSelection) {
                setSelectedBraceletId("");
            }
        }
        // También, si es 'under6' y cambia la fecha, forzar la selección del Baby Park correcto para la nueva fecha
        if (
            clientType === "under6" &&
            filteredBabyParkBraceletProduct &&
            selectedBraceletId !== filteredBabyParkBraceletProduct.id
        ) {
            setSelectedBraceletId(filteredBabyParkBraceletProduct.id);
        }
    }, [
        fecha,
        isWeekday,
        isWeekend,
        PRODUCTS,
        clientType,
        selectedBraceletProduct,
        filteredBabyParkBraceletProduct,
        filteredHourlyBraceletsProducts,
        selectedBraceletId,
    ]);

    // --- Lógica para añadir ítems al carrito ---
    const handleAddToCart = () => {
        setMensaje("");

        // Validar que la fecha no sea ni lunes (1) ni domingo (0) si quieres solo martes a jueves
        // o si es domingo, que el precio sea de fin de semana
        const dayOfWeekForValidation = getDayOfWeek(fecha);
        if (dayOfWeekForValidation === 1) {
            // Lunes
            setMensaje("No se pueden comprar brazaletes para los Lunes.");
            return;
        }
        // Si la fecha seleccionada no es ni entre semana ni fin de semana según tus brazaletes cargados
        if (!isWeekday && !isWeekend) {
            setMensaje(
                "No hay brazaletes disponibles para la fecha seleccionada. Por favor, elige una fecha entre Martes y Domingo."
            );
            return;
        }

        if (!selectedBraceletProduct) {
            setMensaje("Por favor, selecciona un brazalete antes de añadir.");
            return;
        }

        // Los calcetines son obligatorios si hay un brazalete
        if (selectedBraceletProduct && !selectedSockProduct) {
            setMensaje(
                "Las medias son obligatorias con la compra de un brazalete. Por favor, selecciona una talla."
            );
            return;
        }

        const itemsToAdd = [];

        // Asegúrate de que el precio del brazalete en el carrito sea el correcto para el día seleccionado
        const braceletPrice = selectedBraceletProduct.price; // Este ya debería ser el precio correcto gracias al filtrado
        const braceletCartItem = {
            uniqueId: Date.now() + "-bracelet-" + selectedBraceletProduct.id,
            product: { ...selectedBraceletProduct, price: braceletPrice }, // Asegura que el precio en el carrito sea el correcto
            quantity: braceletQuantity,
            selectedDate: fecha,
            clientType: clientType,
        };
        itemsToAdd.push(braceletCartItem);

        // Siempre añadir calcetines si se seleccionó un brazalete y un calcetín
        if (selectedSockProduct) {
            const sockCartItem = {
                uniqueId: Date.now() + "-sock-" + selectedSockProduct.id,
                product: { ...selectedSockProduct }, // El precio de la media ya es 1.50
                quantity: sockQuantity, // Siempre 1
            };
            itemsToAdd.push(sockCartItem);
        }

        setCartItems((prevCartItems) => [...prevCartItems, ...itemsToAdd]);
        setMensaje("¡Productos añadidos al carrito!");

        // Reiniciar el formulario de selección después de añadir al carrito
        setClientType(null);
        setSelectedBraceletId("");
        setBraceletQuantity(1);
        setSelectedSockTallaId("");
        setSockQuantity(1);
    };

    // --- Lógica para eliminar un ítem del carrito ---
    const handleRemoveFromCart = (uniqueIdToRemove) => {
        setCartItems((prevCartItems) =>
            prevCartItems.filter((item) => item.uniqueId !== uniqueIdToRemove)
        );
    };

    // --- Lógica para ajustar la cantidad de un ítem en el carrito ---
    const handleUpdateCartItemQuantity = (uniqueIdToUpdate, newQuantity) => {
        setCartItems((prevCartItems) =>
            prevCartItems.map((item) =>
                item.uniqueId === uniqueIdToUpdate
                    ? { ...item, quantity: Math.max(1, newQuantity) }
                    : item
            )
        );
    };

    // --- Lógica para el submit final del carrito a Laravel ---
    const handleSubmitCheckout = () => {
        // Validación frontend del carrito
        if (cartItems.length === 0) {
            setMensaje("Tu carrito está vacío. Por favor, añade productos.");
            return;
        }

        // Construir el array 'items' con la estructura exacta que tu backend espera
        // Mapea tus cartItems a { product_id, quantity }
        const itemsToSubmit = cartItems.map((item) => ({
            product_id: item.product.id,
            quantity: item.quantity,
        }));

        const dataToSend = {
            fecha: fecha,
            items: itemsToSubmit,
        };

        router.post("/tienda", dataToSend);
    };

    // Calcular el total del carrito para mostrar en el frontend
    const totalCartPrice = cartItems.reduce(
        (sum, item) => sum + item.product.price * item.quantity,
        0
    );

    return (
        <Layout>
            <BannerHero
                img="/img/home/BrincaEste.jpg"
                title="COMPRA TUS ENTRADAS AQUÍ!"
            />
            <div className="py-8 container mx-auto px-4 sm:px-6 lg:px-8">
                <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    {/* Columna de Selección de Productos */}
                    <div className="bg-white shadow-lg rounded-lg overflow-hidden p-6">
                        <h2 className="text-2xl font-bold text-gray-800 mb-6">
                            Selecciona tus Productos
                        </h2>

                        {/* Paso 1: Selección de Fecha */}
                        <div className="form-group mb-6 border-b pb-4">
                            <label
                                htmlFor="fecha"
                                className="block text-gray-700 text-lg font-semibold mb-3"
                            >
                                Fecha de Visita:
                            </label>
                            <input
                                type="date"
                                id="fecha"
                                className="mt-1 block w-full p-2 border rounded-md focus:ring-blue-500 focus:border-blue-500"
                                value={fecha}
                                onChange={(e) => setFecha(e.target.value)}
                                required
                            />
                        </div>

                        {/* Paso 2: Selección de Tipo de Cliente */}
                        <div className="mb-6 border-b pb-4">
                            <p className="block text-gray-700 text-lg font-semibold mb-3">
                                ¿Para quién es el brazalete?
                            </p>
                            <div className="flex flex-col sm:flex-row gap-4 justify-center">
                                <button
                                    type="button"
                                    onClick={() =>
                                        handleClientTypeChange("under6")
                                    }
                                    className={`py-3 px-6 rounded-lg font-medium transition duration-300
                                        ${
                                            clientType === "under6"
                                                ? "bg-indigo-600 text-white shadow-md"
                                                : "bg-indigo-100 text-indigo-700 hover:bg-indigo-200"
                                        }
                                    `}
                                >
                                    Niño/a &lt; 6 años (Área Baby Park)
                                </button>
                                <button
                                    type="button"
                                    onClick={() =>
                                        handleClientTypeChange("adultOrOver6")
                                    }
                                    className={`py-3 px-6 rounded-lg font-medium transition duration-300
                                        ${
                                            clientType === "adultOrOver6"
                                                ? "bg-emerald-600 text-white shadow-md"
                                                : "bg-emerald-100 text-emerald-700 hover:bg-emerald-200"
                                        }
                                    `}
                                >
                                    Mayor de 6 años o Adulto (Trampolines)
                                </button>
                            </div>
                        </div>

                        {/* Paso 3: Selección de Brazalete y Medias (Condicional) */}
                        {clientType && (
                            <div className="mt-6">
                                {/* Sección de Brazalete Baby Park */}
                                {clientType === "under6" &&
                                    filteredBabyParkBraceletProduct && (
                                        <div className="bg-blue-50 p-4 rounded-md mb-6 border border-blue-200">
                                            <h3 className="text-xl font-semibold text-blue-800 mb-3">
                                                Brazalete Baby Park
                                            </h3>
                                            <p className="text-blue-700 mb-2">
                                                **
                                                {
                                                    filteredBabyParkBraceletProduct.name
                                                }
                                                **:{" "}
                                                {
                                                    filteredBabyParkBraceletProduct.description
                                                }
                                                <span className="font-bold ml-2">
                                                    $
                                                    {filteredBabyParkBraceletProduct.price.toFixed(
                                                        2
                                                    )}
                                                </span>
                                            </p>
                                            <div className="flex items-center gap-2 mt-2">
                                                <label
                                                    htmlFor="babyParkQty"
                                                    className="text-gray-700"
                                                >
                                                    Cantidad:
                                                </label>
                                                <input
                                                    type="number"
                                                    id="babyParkQty"
                                                    min="1"
                                                    value={braceletQuantity}
                                                    onChange={(e) =>
                                                        setBraceletQuantity(
                                                            parseInt(
                                                                e.target.value
                                                            ) || 1
                                                        )
                                                    }
                                                    className="w-20 p-2 border rounded-md text-center"
                                                />
                                            </div>
                                        </div>
                                    )}
                                {/* Si no se encontró el Baby Park para la fecha seleccionada */}
                                {clientType === "under6" &&
                                    !filteredBabyParkBraceletProduct && (
                                        <p className="text-red-500 text-sm mt-2">
                                            No hay brazaletes Baby Park
                                            disponibles para la fecha
                                            seleccionada.
                                        </p>
                                    )}

                                {/* Sección de Brazaletes por Hora */}
                                {clientType === "adultOrOver6" && (
                                    <div className="bg-green-50 p-4 rounded-md mb-6 border border-green-200">
                                        <h3 className="text-xl font-semibold text-green-800 mb-3">
                                            Selecciona tu Franja Horaria
                                            (Brazalete)
                                        </h3>
                                        {filteredHourlyBraceletsProducts.length >
                                        0 ? (
                                            <p className="text-green-700 mb-3">
                                                Precio por brazalete: $
                                                {filteredHourlyBraceletsProducts[0]?.price.toFixed(
                                                    2
                                                )}
                                            </p>
                                        ) : (
                                            <p className="text-red-500 text-sm mb-3">
                                                No hay brazaletes disponibles
                                                para la fecha seleccionada.
                                            </p>
                                        )}

                                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-4">
                                            {filteredHourlyBraceletsProducts.map(
                                                (bracelet) => (
                                                    <button
                                                        key={bracelet.id}
                                                        type="button"
                                                        onClick={() =>
                                                            setSelectedBraceletId(
                                                                bracelet.id
                                                            )
                                                        }
                                                        className={`p-3 border rounded-md text-sm font-medium text-center transition duration-200
                                                        ${
                                                            selectedBraceletId ===
                                                            bracelet.id
                                                                ? "bg-green-600 text-white shadow-md"
                                                                : "bg-white text-green-700 hover:bg-green-100 border-green-300"
                                                        }
                                                    `}
                                                    >
                                                        {bracelet.description}{" "}
                                                        <br /> (
                                                        {bracelet.name
                                                            .replace(
                                                                "Brazalete ",
                                                                ""
                                                            )
                                                            .replace(
                                                                " (Martes a Jueves)",
                                                                ""
                                                            )
                                                            .replace(
                                                                " (Viernes a Domingo)",
                                                                ""
                                                            )}
                                                        )
                                                    </button>
                                                )
                                            )}
                                        </div>
                                        {selectedBraceletId && (
                                            <div className="flex items-center gap-2 mt-3">
                                                <label
                                                    htmlFor="hourlyQty"
                                                    className="text-gray-700"
                                                >
                                                    Cantidad:
                                                </label>
                                                <input
                                                    type="number"
                                                    id="hourlyQty"
                                                    min="1"
                                                    value={braceletQuantity}
                                                    onChange={(e) =>
                                                        setBraceletQuantity(
                                                            parseInt(
                                                                e.target.value
                                                            ) || 1
                                                        )
                                                    }
                                                    className="w-20 p-2 border rounded-md text-center"
                                                />
                                            </div>
                                        )}
                                        {!selectedBraceletId &&
                                            filteredHourlyBraceletsProducts.length >
                                                0 && (
                                                <p className="text-red-500 text-sm mt-2">
                                                    Por favor, selecciona una
                                                    franja horaria.
                                                </p>
                                            )}
                                        {clientType === "adultOrOver6" &&
                                            filteredHourlyBraceletsProducts.length ===
                                                0 && (
                                                <p className="text-red-500 text-sm mt-2">
                                                    No hay brazaletes de
                                                    trampolines disponibles para
                                                    la fecha seleccionada.
                                                </p>
                                            )}
                                    </div>
                                )}

                                {/* Sección de Medias Especiales (MODIFICADA) */}
                                <div className="bg-yellow-50 p-4 rounded-md border border-yellow-200 mb-6">
                                    <h3 className="text-xl font-semibold text-yellow-800 mb-3">
                                        Medias Especiales
                                    </h3>
                                    <p className="text-yellow-700 mb-2">
                                        Obligatorias para usar los trampolines
                                        (compra única por brazalete).
                                    </p>

                                    <div className="mb-3">
                                        <label
                                            htmlFor="sockTalla"
                                            className="block text-gray-700 mb-1"
                                        >
                                            Selecciona Talla:
                                        </label>
                                        <select
                                            id="sockTalla"
                                            value={selectedSockTallaId}
                                            onChange={(e) =>
                                                setSelectedSockTallaId(
                                                    parseInt(e.target.value) ||
                                                        ""
                                                )
                                            } // Convertir a int, si es vacío, dejarlo vacío
                                            className="w-full p-2 border rounded-md bg-white"
                                            disabled={
                                                !selectedBraceletId ||
                                                socksProducts.length === 0
                                            } // Deshabilitar si no hay brazalete o no hay medias
                                        >
                                            <option value="">
                                                Selecciona la talla de medias
                                            </option>
                                            {socksProducts.map((sock) => (
                                                <option
                                                    key={sock.id}
                                                    value={sock.id}
                                                >
                                                    {sock.name} - $
                                                    {sock.price.toFixed(2)}
                                                </option>
                                            ))}
                                        </select>
                                        {selectedBraceletId &&
                                            !selectedSockTallaId &&
                                            socksProducts.length > 0 && (
                                                <p className="text-red-500 text-sm mt-1">
                                                    Por favor, selecciona una
                                                    talla de medias.
                                                </p>
                                            )}
                                        {socksProducts.length === 0 && (
                                            <p className="text-red-500 text-sm mt-1">
                                                No hay medias disponibles.
                                            </p>
                                        )}
                                    </div>
                                    {selectedSockTallaId && (
                                        <div className="flex items-center gap-2">
                                            <label
                                                htmlFor="sockQty"
                                                className="text-gray-700"
                                            >
                                                Cantidad:
                                            </label>
                                            <span className="font-semibold text-gray-800">
                                                {sockQuantity}
                                            </span>
                                        </div>
                                    )}
                                </div>

                                {/* Botón Añadir al Carrito (para el ítem seleccionado actualmente) */}
                                <div className="mt-6 text-center">
                                    <button
                                        type="button"
                                        onClick={handleAddToCart}
                                        disabled={
                                            !selectedBraceletId || // No hay brazalete seleccionado
                                            (selectedBraceletId &&
                                                !selectedSockTallaId) || // Hay brazalete pero no calcetín
                                            (clientType === "under6" &&
                                                !filteredBabyParkBraceletProduct) || // Si es Baby Park y no hay producto
                                            (clientType === "adultOrOver6" &&
                                                filteredHourlyBraceletsProducts.length ===
                                                    0) // Si es Trampolines y no hay productos
                                        }
                                        className={`py-3 px-8 rounded-lg text-lg font-bold transition duration-300
                                            ${
                                                !selectedBraceletId ||
                                                (selectedBraceletId &&
                                                    !selectedSockTallaId) ||
                                                (clientType === "under6" &&
                                                    !filteredBabyParkBraceletProduct) ||
                                                (clientType ===
                                                    "adultOrOver6" &&
                                                    filteredHourlyBraceletsProducts.length ===
                                                        0)
                                                    ? "bg-gray-400 cursor-not-allowed"
                                                    : "bg-blue-600 text-white hover:bg-blue-700 shadow-lg"
                                            }
                                        `}
                                    >
                                        Añadir al Carrito
                                    </button>
                                </div>
                            </div>
                        )}
                        {mensaje && (
                            <p className="p-4 text-center text-red-600">
                                {mensaje}
                            </p>
                        )}
                    </div>

                    {/* Columna del Carrito de Compras */}
                    <div className="bg-white shadow-lg rounded-lg overflow-hidden p-6">
                        <h2 className="text-2xl font-bold text-gray-800 mb-6">
                            Tu Carrito ({cartItems.length} ítems)
                        </h2>

                        {cartItems.length === 0 ? (
                            <p className="text-gray-500 text-center py-8">
                                El carrito está vacío. ¡Empieza a añadir
                                productos!
                            </p>
                        ) : (
                            <>
                                <ul className="divide-y divide-gray-200">
                                    {cartItems.map((item) => (
                                        <li
                                            key={item.uniqueId}
                                            className="py-4 flex flex-col sm:flex-row justify-between items-center"
                                        >
                                            <div className="flex-1 text-center sm:text-left mb-2 sm:mb-0">
                                                <p className="font-semibold text-gray-700">
                                                    {item.product.name}
                                                </p>
                                                <p className="text-sm text-gray-500">
                                                    {item.product.description}
                                                </p>
                                                {item.selectedDate && (
                                                    <p className="text-xs text-gray-500">
                                                        Fecha:{" "}
                                                        {item.selectedDate}
                                                    </p>
                                                )}
                                                {item.clientType && (
                                                    <p className="text-xs text-gray-500">
                                                        Tipo:{" "}
                                                        {item.clientType ===
                                                        "under6"
                                                            ? "Niño < 6"
                                                            : "Adulto/Niño > 6"}
                                                    </p>
                                                )}
                                            </div>
                                            <div className="flex items-center gap-3">
                                                <button
                                                    type="button"
                                                    onClick={() =>
                                                        handleUpdateCartItemQuantity(
                                                            item.uniqueId,
                                                            item.quantity - 1
                                                        )
                                                    }
                                                    className="p-1.5 bg-gray-200 rounded-full text-gray-700 hover:bg-gray-300 transition"
                                                    disabled={
                                                        item.quantity <= 1 ||
                                                        item.product
                                                            .category ===
                                                            "Calcetines"
                                                    } // Deshabilitar para calcetines
                                                >
                                                    <svg
                                                        className="w-4 h-4"
                                                        fill="none"
                                                        stroke="currentColor"
                                                        viewBox="0 0 24 24"
                                                        xmlns="http://www.w3.org/2000/svg"
                                                    >
                                                        <path
                                                            strokeLinecap="round"
                                                            strokeLinejoin="round"
                                                            strokeWidth="2"
                                                            d="M20 12H4"
                                                        ></path>
                                                    </svg>
                                                </button>
                                                <span className="font-medium text-gray-800">
                                                    {item.quantity}
                                                </span>
                                                <button
                                                    type="button"
                                                    onClick={() =>
                                                        handleUpdateCartItemQuantity(
                                                            item.uniqueId,
                                                            item.quantity + 1
                                                        )
                                                    }
                                                    className="p-1.5 bg-gray-200 rounded-full text-gray-700 hover:bg-gray-300 transition"
                                                    disabled={
                                                        item.product
                                                            .category ===
                                                        "Calcetines"
                                                    } // Deshabilitar para calcetines
                                                >
                                                    <svg
                                                        className="w-4 h-4"
                                                        fill="none"
                                                        stroke="currentColor"
                                                        viewBox="0 0 24 24"
                                                        xmlns="http://www.w3.org/2000/svg"
                                                    >
                                                        <path
                                                            strokeLinecap="round"
                                                            strokeLinejoin="round"
                                                            strokeWidth="2"
                                                            d="M12 4v16m8-8H4"
                                                        ></path>
                                                    </svg>
                                                </button>
                                                <span className="font-semibold text-gray-800 w-20 text-right">
                                                    $
                                                    {(
                                                        item.product.price *
                                                        item.quantity
                                                    ).toFixed(2)}
                                                </span>
                                                <button
                                                    type="button"
                                                    onClick={() =>
                                                        handleRemoveFromCart(
                                                            item.uniqueId
                                                        )
                                                    }
                                                    className="ml-3 text-red-500 hover:text-red-700 transition"
                                                    title="Eliminar"
                                                >
                                                    <svg
                                                        className="w-5 h-5"
                                                        fill="none"
                                                        stroke="currentColor"
                                                        viewBox="0 0 24 24"
                                                        xmlns="http://www.w3.org/2000/svg"
                                                    >
                                                        <path
                                                            strokeLinecap="round"
                                                            strokeLinejoin="round"
                                                            strokeWidth="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                                        ></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </li>
                                    ))}
                                </ul>

                                <div className="text-right mt-6 pt-4 border-t border-gray-200">
                                    <div className="text-2xl font-bold text-gray-900">
                                        Total: ${totalCartPrice.toFixed(2)}
                                    </div>
                                    <button
                                        type="button"
                                        onClick={() => {
                                            handleSubmitCheckout();
                                        }}
                                        className="mt-4 w-full bg-green-600 text-white py-3 rounded-lg text-lg font-bold hover:bg-green-700 transition shadow-lg"
                                    >
                                        Proceder al Pago
                                    </button>
                                </div>
                            </>
                        )}
                    </div>
                </div>
            </div>
        </Layout>
    );
};

export default Tienda;
