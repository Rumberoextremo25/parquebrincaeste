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

    // MODIFICACIÓN: Determina si la fecha seleccionada es de fin de semana (Sábado, Domingo)
    const isWeekend =
        getDayOfWeek(fecha) === 6 || getDayOfWeek(fecha) === 0; // 6=Sábado, 0=Domingo
    // MODIFICACIÓN: Determina si la fecha seleccionada es de entre semana (Lunes a Viernes)
    const isWeekday = getDayOfWeek(fecha) >= 1 && getDayOfWeek(fecha) <= 5; // 1=Lunes, 2=Martes, ..., 5=Viernes

    // Precio base de los brazaletes según el día
    const BRACELET_PRICE_WEEKDAY = 5.0; // Lunes a Viernes
    const BRACELET_PRICE_WEEKEND = 6.0; // Sábado y Domingo

    // MODIFICACIÓN: Encuentra el producto "Pass Baby Park" sin filtrar por nombre de día
    const baseBabyParkBraceletProduct = PRODUCTS.find(
        (p) => p.category === "Pass Baby Park"
    );

    // MODIFICACIÓN: Encuentra los productos "Brazalete" sin filtrar por nombre de día
    const baseHourlyBraceletsProducts = PRODUCTS.filter(
        (p) => p.category === "Brazalete"
    );

    // Crea un objeto del brazalete Baby Park con el precio ajustado
    const adjustedBabyParkBraceletProduct = baseBabyParkBraceletProduct
        ? {
              ...baseBabyParkBraceletProduct,
              price: isWeekend
                  ? BRACELET_PRICE_WEEKEND
                  : BRACELET_PRICE_WEEKDAY,
              // Opcional: podrías ajustar el nombre si lo necesitas para mostrar en UI
              // name: `Brazalete Baby Park (${isWeekend ? 'Fin de Semana' : 'Entre Semana'})`
          }
        : null;

    // Crea un array de brazaletes por hora con los precios ajustados
    const adjustedHourlyBraceletsProducts = baseHourlyBraceletsProducts.map(
        (p) => ({
            ...p,
            price: isWeekend
                ? BRACELET_PRICE_WEEKEND
                : BRACELET_PRICE_WEEKDAY,
            // Opcional: podrías ajustar el nombre si lo necesitas para mostrar en UI
            // name: `${p.name.replace(/\s\(.*\)/, '')} (${isWeekend ? 'Fin de Semana' : 'Entre Semana'})`
        })
    );

    // Filtra las medias (calcetines) - su precio ya es fijo en $1.50 desde el seeder
    const socksProducts = PRODUCTS.filter((p) => p.category === "Medias");

    // MODIFICACIÓN: selectedBraceletProduct ahora se obtiene de los productos ajustados
    const selectedBraceletProduct =
        adjustedBabyParkBraceletProduct?.id === selectedBraceletId
            ? adjustedBabyParkBraceletProduct
            : adjustedHourlyBraceletsProducts.find(
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
        if (type === "under6" && adjustedBabyParkBraceletProduct) { // MODIFICACIÓN
            setSelectedBraceletId(adjustedBabyParkBraceletProduct.id); // MODIFICACIÓN
            setBraceletQuantity(1);
        }
    };

    // MODIFICACIÓN IMPORTANTE: useEffect para forzar la selección de calcetines
    useEffect(() => {
        // Verificar si se ha seleccionado algún brazalete (sea Baby Park o por hora)
        const isAnyBraceletSelected = selectedBraceletId !== "";

        if (isAnyBraceletSelected && socksProducts.length > 0) {
            const currentSelectedSockApplies = socksProducts.some(
                (sock) => sock.id === selectedSockTallaId
            );

            if (!selectedSockTallaId || !currentSelectedSockApplies) {
                setSelectedSockTallaId(String(socksProducts[0].id));
            }
            setSockQuantity(1); // Asegurar que la cantidad sea 1
        } else if (!isAnyBraceletSelected) {
            setSelectedSockTallaId("");
            setSockQuantity(1);
        }
    }, [selectedBraceletId, socksProducts, selectedSockTallaId]);

    // Recalcular los productos cuando la fecha cambie para obtener los precios correctos
    useEffect(() => {
        // Al cambiar la fecha, re-evaluar si el brazalete actualmente seleccionado sigue siendo válido
        // para el nuevo día (entre semana/fin de semana).
        // Si no, deseleccionarlo.
        if (selectedBraceletId) { // Aquí solo verificamos si hay un ID seleccionado
            let isValidSelection = false;
            // Si el seleccionado es Baby Park, verifica que exista el producto ajustado
            if (selectedBraceletId === adjustedBabyParkBraceletProduct?.id) { // MODIFICACIÓN
                isValidSelection = !!adjustedBabyParkBraceletProduct;
            } else { // Si es un brazalete por hora, verifica que el ID esté entre los ajustados
                isValidSelection = adjustedHourlyBraceletsProducts.some( // MODIFICACIÓN
                    (p) => p.id === selectedBraceletId
                );
            }

            if (!isValidSelection) {
                setSelectedBraceletId("");
            }
        }
        // También, si es 'under6' y cambia la fecha, forzar la selección del Baby Park correcto para la nueva fecha
        if (
            clientType === "under6" &&
            adjustedBabyParkBraceletProduct && // MODIFICACIÓN
            selectedBraceletId !== adjustedBabyParkBraceletProduct.id // MODIFICACIÓN
        ) {
            setSelectedBraceletId(adjustedBabyParkBraceletProduct.id); // MODIFICACIÓN
        }
    }, [
        fecha,
        isWeekday,
        isWeekend,
        PRODUCTS, // Aunque PRODUCTS no cambie, sus derivados sí lo hacen
        clientType,
        selectedBraceletId,
        adjustedBabyParkBraceletProduct, // MODIFICACIÓN
        adjustedHourlyBraceletsProducts, // MODIFICACIÓN
    ]);

    // --- Lógica para añadir ítems al carrito ---
    const handleAddToCart = () => {
        setMensaje("");

        const dayOfWeekForValidation = getDayOfWeek(fecha);

        // Ya no necesitamos validar si es lunes específicamente,
        // ya que lunes a viernes tienen el mismo precio.
        // Solo necesitamos que sea un día válido para la compra.
        if (!isWeekday && !isWeekend) {
            setMensaje(
                "No hay brazaletes disponibles para la fecha seleccionada. Por favor, elige una fecha válida (Lunes a Domingo)."
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

        // El precio del brazalete ya estará ajustado en selectedBraceletProduct
        const braceletPrice = selectedBraceletProduct.price;
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
                                    adjustedBabyParkBraceletProduct && ( // MODIFICACIÓN
                                        <div className="bg-blue-50 p-4 rounded-md mb-6 border border-blue-200">
                                            <h3 className="text-xl font-semibold text-blue-800 mb-3">
                                                Brazalete Baby Park
                                            </h3>
                                            <p className="text-blue-700 mb-2">
                                                **
                                                {
                                                    adjustedBabyParkBraceletProduct.name // MODIFICACIÓN
                                                }
                                                **:{" "}
                                                {
                                                    adjustedBabyParkBraceletProduct.description // MODIFICACIÓN
                                                }
                                                <span className="font-bold ml-2">
                                                    ${" "}
                                                    {adjustedBabyParkBraceletProduct.price.toFixed( // MODIFICACIÓN
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
                                    !adjustedBabyParkBraceletProduct && ( // MODIFICACIÓN
                                        <p className="text-red-500 text-sm mt-2">
                                            No hay brazaletes Baby Park
                                            disponibles.
                                        </p>
                                    )}

                                {/* Sección de Brazaletes por Hora */}
                                {clientType === "adultOrOver6" && (
                                    <div className="bg-green-50 p-4 rounded-md mb-6 border border-green-200">
                                        <h3 className="text-xl font-semibold text-green-800 mb-3">
                                            Selecciona tu Franja Horaria
                                            (Brazalete)
                                        </h3>
                                        {adjustedHourlyBraceletsProducts.length > // MODIFICACIÓN
                                        0 ? (
                                            <p className="text-green-700 mb-3">
                                                Precio por brazalete: $
                                                {adjustedHourlyBraceletsProducts[0]?.price.toFixed( // MODIFICACIÓN
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
                                            {adjustedHourlyBraceletsProducts.map( // MODIFICACIÓN
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
                                                        {
                                                            // Aquí podrías mostrar el nombre ajustado si lo hiciste,
                                                            // o simplemente la descripción si es suficiente.
                                                            // Si el nombre original tiene " (Martes a Jueves)",
                                                            // quítalo al mostrarlo aquí.
                                                            bracelet.name.replace("Brazalete ", "")
                                                                .replace(" (Martes a Jueves)", "")
                                                                .replace(" (Viernes a Domingo)", "")
                                                        }
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
                                            adjustedHourlyBraceletsProducts.length > // MODIFICACIÓN
                                                0 && (
                                                <p className="text-red-500 text-sm mt-2">
                                                    Por favor, selecciona una
                                                    franja horaria.
                                                </p>
                                            )}
                                        {clientType === "adultOrOver6" &&
                                            adjustedHourlyBraceletsProducts.length === // MODIFICACIÓN
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
                                                !selectedBraceletId || // Deshabilitar si no hay brazalete o no hay medias
                                                socksProducts.length === 0
                                            }
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
                                                !adjustedBabyParkBraceletProduct) || // Si es Baby Park y no hay producto
                                            (clientType === "adultOrOver6" &&
                                                adjustedHourlyBraceletsProducts.length ===
                                                    0) // Si es Trampolines y no hay productos
                                        }
                                        className="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-full transition duration-300 ease-in-out shadow-lg text-lg"
                                    >
                                        Añadir al Carrito
                                    </button>
                                </div>
                                {mensaje && (
                                    <p className="text-center mt-4 text-sm text-gray-600">
                                        {mensaje}
                                    </p>
                                )}
                            </div>
                        )}
                    </div>

                    {/* Columna del Carrito de Compras */}
                    <div className="bg-gray-50 shadow-lg rounded-lg overflow-hidden p-6">
                        <h2 className="text-2xl font-bold text-gray-800 mb-6">
                            Tu Carrito
                        </h2>
                        {cartItems.length === 0 ? (
                            <p className="text-gray-600">
                                Tu carrito está vacío. ¡Añade algunos productos!
                            </p>
                        ) : (
                            <div>
                                {cartItems.map((item) => (
                                    <div
                                        key={item.uniqueId}
                                        className="flex items-center justify-between border-b py-3 last:border-b-0"
                                    >
                                        <div>
                                            <p className="font-semibold text-gray-800">
                                                {item.product.name} (
                                                {item.selectedDate})
                                            </p>
                                            <p className="text-sm text-gray-600">
                                                {item.product.description}
                                            </p>
                                            <p className="text-sm text-gray-600">
                                                ${item.product.price.toFixed(2)}{" "}
                                                x {item.quantity}
                                            </p>
                                        </div>
                                        <div className="flex items-center gap-2">
                                            <button
                                                onClick={() =>
                                                    handleUpdateCartItemQuantity(
                                                        item.uniqueId,
                                                        item.quantity - 1
                                                    )
                                                }
                                                className="bg-red-200 text-red-700 p-1 rounded-full w-6 h-6 flex items-center justify-center text-sm font-bold"
                                            >
                                                -
                                            </button>
                                            <span className="font-semibold">
                                                {item.quantity}
                                            </span>
                                            <button
                                                onClick={() =>
                                                    handleUpdateCartItemQuantity(
                                                        item.uniqueId,
                                                        item.quantity + 1
                                                    )
                                                }
                                                className="bg-green-200 text-green-700 p-1 rounded-full w-6 h-6 flex items-center justify-center text-sm font-bold"
                                            >
                                                +
                                            </button>
                                            <button
                                                onClick={() =>
                                                    handleRemoveFromCart(
                                                        item.uniqueId
                                                    )
                                                }
                                                className="ml-3 text-red-500 hover:text-red-700"
                                                title="Eliminar"
                                            >
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    className="h-5 w-5"
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                >
                                                    <path
                                                        fillRule="evenodd"
                                                        d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm6 0a1 1 0 11-2 0v6a1 1 0 112 0V8z"
                                                        clipRule="evenodd"
                                                    />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                ))}
                                <div className="mt-6 pt-4 border-t-2 border-gray-200 flex justify-between items-center">
                                    <span className="text-xl font-bold text-gray-800">
                                        Total:
                                    </span>
                                    <span className="text-xl font-bold text-blue-600">
                                        ${totalCartPrice.toFixed(2)}
                                    </span>
                                </div>
                                <div className="mt-6 text-center">
                                    <button
                                        type="button"
                                        onClick={handleSubmitCheckout}
                                        className="bg-purple-600 hover:bg-purple-700 text-white font-bold py-3 px-8 rounded-full transition duration-300 ease-in-out shadow-lg text-lg"
                                        disabled={cartItems.length === 0}
                                    >
                                        Proceder al Pago
                                    </button>
                                </div>
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </Layout>
    );
};

export default Tienda;
