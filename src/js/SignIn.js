let email = document.getElementById('email');
let password = document.getElementById('password');
let infoMessage = document.getElementById('infoMessage');

document.getElementById('signInForm').addEventListener('submit', async function(e) {
  e.preventDefault();

  if (email && email.value && password && password.value) {
    infoMessage.textContent = "";

    const formData = {
      email: email.value,
      pass: password.value
    };

    const userResult = await userLogin(formData);

    if (userResult.status === 'success') {

      const getCart = await getShoppingCart(userResult.id_cliente);

      if (getCart.status === 'success') {

        window.location.href = "../index.php";

      } else {

        infoMessage.textContent = result.message || "Error getting shopping cart!";

      }

    } else if (userResult.message === 'User not found') {
      
      const adminResult = await adminLogin(formData);
 
      if (adminResult.status === 'success') {
      
        window.location.href = "Admin/Dashboard.php";
      
      } else {
        
        infoMessage.textContent = adminResult.message || "Admin login failed!";
      
      }
    
    } else {
    
      infoMessage.textContent = userResult.message || "Login failed!";
    
    }
  
  } else {
  
    infoMessage.textContent = "Please fill all the fields!";
  
  }
});

async function userLogin(formData) {
  const response = await fetch('../client/login.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify(formData)
  });

  return await response.json();
}

async function adminLogin(formData) {
  const response = await fetch('../admin/login.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify(formData)
  });

  return await response.json();
}

async function getShoppingCart(id_cliente) {

  try {
    const response = await fetch('../client/getShoppingCart.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({id_cliente: id_cliente})
    });

    const data = await response.json();
    
    return data;
  
  } catch (error) {
    
    return null;
  
  }
}
