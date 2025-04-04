# Prueba Técnica – Desarrollador WordPress

Este repositorio contiene la entrega de la prueba técnica desarrollada en WordPress + WooCommerce.
---

## Tecnologías Utilizadas

- WordPress 6.7.2
- WooCommerce
- Theme: Storefront (gratuito)

---

## Plugins Utilizados

- **WooCommerce**
- **Flexible Shipping**
- **PDF Invoices & Packing Slips for WooCommerce**
- **WooCommerce Role Based Pricing**

---

## Funcionalidades Implementadas

### 1. Instalación y Configuración de WordPress
Se realizó una instalación limpia con la última versión de WordPress y el theme Storefront.

### 2. Implementación de WooCommerce
Configuración básica de tienda, productos, impuestos, y páginas predeterminadas de WooCommerce.

### 3. Personalización de la Tienda – Cupones de Descuento

Se desarrolló una función personalizada que:

- Detecta si es la primera compra del usuario.
- Genera automáticamente un cupón de bienvenida (`bienvenido+nombre+20`).
- El cupón ofrece $20.000 de descuento y expira en 30 días.
- Se envía por correo al usuario.

### 4. Envíos Diferenciados por Ciudad

Configurado con el plugin **Flexible Shipping**:

- **Antioquia**: Envío gratis.
- **Bogotá / Cundinamarca**: $20.000 + impuestos.
- **Cartagena / Costa Atlántica**: $35.000 + impuestos.

### 5. Precios Diferenciados por Rol de Usuario

Se utilizó el plugin **WooCommerce Role Based Pricing** para:

- Crear el rol `Mayorista`.
- Ajustar los precios según el rol.

### 6. Correos Electrónicos Personalizados

- Se configuraron plantillas de WooCommerce para reflejar correctamente cada estado del pedido.
- Se utilizó SMTP y Mailhog para pruebas de envío de correos.

### 7. Gestión de Pedidos y Estados Automáticos

- Se probaron los flujos automáticos de WooCommerce.
- Cada cambio de estado de pedido activa un correo correspondiente.

### 8. Área Personalizada para el Usuario

Se usaron las secciones predeterminadas del WooCommerce
