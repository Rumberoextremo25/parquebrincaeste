import React from 'react';

const Notification = ({ message, onClose }) => {
    if (!message) return null;

    return (
        <div className="fixed top-5 right-5 bg-green-500 text-white p-4 rounded shadow-lg">
            <div>{message}</div>
            <button onClick={onClose} className="mt-2 text-white underline">
                Cerrar
            </button>
        </div>
    );
};

export default Notification;