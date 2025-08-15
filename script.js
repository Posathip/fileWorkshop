document.addEventListener('DOMContentLoaded', () => {
  const tableBody = document.querySelector('#studentTable tbody');
  const errorMessage = document.getElementById('errorMessage');
  const refreshBtn = document.getElementById('refreshBtn');

  function fetchData() {
    errorMessage.style.display = 'none';
    tableBody.innerHTML = '<tr><td colspan="8" class="loading">Loading data...</td></tr>';

    axios.get('https://testconso.consolutechfarm.com/phpfile/api/getalldata.php')
      .then(response => {
        if (response.data.status === 'success' && Array.isArray(response.data.data)) {
          const students = response.data.data;
          if (students.length === 0) {
            tableBody.innerHTML = '<tr><td colspan="8" style="text-align:center;">No data found.</td></tr>';
            return;
          }
          
          const rows = students.map(student => `
            <tr>
              <td>${student.id}</td>
              <td>${student.email}</td>
              <td>${student.name}</td>
              <td>${student.departmentName}</td>
              <td>${student.studentID}</td>
              <td>${student.roleUserLevel}</td>
              <td>${student.createdAt}</td>
              <td>${student.updatedAt}</td>
            </tr>
          `).join('');
          
          tableBody.innerHTML = rows;
        } else {
          throw new Error('Failed to load data');
        }
      })
      .catch(error => {
        tableBody.innerHTML = '';
        errorMessage.style.display = 'block';
        errorMessage.textContent = 'Error loading data: ' + error.message;
      });
  }

  refreshBtn.addEventListener('click', fetchData);


  fetchData();
});
