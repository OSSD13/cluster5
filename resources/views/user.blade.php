<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Header UI</title>
    <link rel="stylesheet" href="styles.css"> <!-- เชื่อมกับไฟล์ CSS -->
</head>
<style>
    /* Reset */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: Arial, sans-serif;
}

/* Header */
.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: white;
    padding: 15px 20px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

/* Logo Section */
.logo-container {
    display: flex;
    align-items: center;
    gap: 10px;
}

.logo {
    height: 50px;
}

.logo-text h1 {
    font-size: 18px;
    font-weight: bold;
    color: #333;
    margin: 0;
}

.logo-text p {
    font-size: 14px;
    color: #444444;
    margin: 0;
    align-items: left;
}

/* Hamburger Menu */
.menu-btn {
    background: none;
    border: none;
    display: flex;
    flex-direction: column;
    gap: 5px;
    cursor: pointer;
}

.menu-btn .bar {
    width: 30px;
    height: 3px;
    background: black;
}
.iconMyOrder {
    width: 129px;
        height: 64px;
        margin-bottom: 10px;
        align-items: left;
    
    }
    .arrow-line {
        width: 1px;
        height: 65px;
        background: black;
        transform: rotate(0deg);
    }

</style>
<body>

    <header class="header">
        <!-- Logo Section -->
        <div class="logo-container" >
            <img src="/assets/img/MY LOCITION.png"  class="iconMyOrder" alt="">
            <hr class="arrow-line">
        </div>

        <!-- Menu Button (Hamburger) -->
        <button class="menu-btn">
            <div class="bar"></div>
            <div class="bar"></div>
            <div class="bar"></div>
        </button>
    </header>

</body>
</html>