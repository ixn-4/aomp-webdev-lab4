<?php
session_start();

// start session
if (!isset($_SESSION['lists'])) {
    $_SESSION['lists'] = [];
}

$error_message = "";
$success_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    if ($_POST['action'] === 'create_list') {
        
        $list_name = trim($_POST['list_name']);

        if (empty($list_name)) {
            $error_message = "List name cannot be empty.";
        } else {
            // checks duplicates
            $is_duplicate = false;
            foreach ($_SESSION['lists'] as $existing_list) {
                if (strtolower($existing_list['name']) === strtolower($list_name)) {
                    $is_duplicate = true;
                    break;
                }
            }


            if ($is_duplicate) {
                $error_message = "A list with this name already exists.";
            } else {
                // save to session
                $_SESSION['lists'][] = [
                    'id' => uniqid(),
                    'name' => htmlspecialchars($list_name),
                    'tasks' => []
                ];
                $success_message = "List created successfully!";
            }
        }
    }
    if ($_POST['action'] === 'add_task') {
        $list_id = $_POST['list_id'];
        $new_task = trim($_POST['new_task']);

        if (empty($new_task)) {
            $error_message = "Task cannot be empty.";
        } else {
            // Find the list
            foreach ($_SESSION['lists'] as $key => $list) {
                if ($list['id'] === $list_id) {
                    $is_task_duplicate = false;
                    foreach ($list['tasks'] as $existing_task) {
                        if (strtolower($existing_task['task_name']) === strtolower($new_task)) {
                            $is_task_duplicate = true;
                            break;
                        }
                    }

                    if ($is_task_duplicate) {
                        $error_message = "This task already exists in this list.";
                    } else {
                        $_SESSION['lists'][$key]['tasks'][] = [
                            'task_name' => htmlspecialchars($new_task),
                            'completed' => false
                        ];
                        $success_message = "Task added successfully!";
                    }
                    break;
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>TOODUE - To-Do List</title>
  
  <link rel="stylesheet" href="style.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body>

  <main class="container">
    <!----------------------HERO SECTION---------------------->
    <section class="hero-section">
      <div class="hero-content">
        <h1 class="main-title">
          <img src="images/logo.svg" alt="TOODUE Logo" class="brand-logo">
          TOODUE
        </h1>
        <p class="subtitle">A productive day keeps due dates away.</p>
      </div>
    </section>

    <!----------------------FORM SECTION---------------------->
    <section class="form-section">
       <?php if ($error_message): ?>
          <div style="color: #d32f2f; background: #ffebee; padding: 10px; border-radius: 5px; margin-bottom: 15px; border-left: 4px solid #d32f2f;">
              <?php echo $error_message; ?>
          </div>
      <?php endif; ?>

      <?php if ($success_message): ?>
          <div style="color: #2e7d32; background: #e8f5e9; padding: 10px; border-radius: 5px; margin-bottom: 15px; border-left: 4px solid #2e7d32;">
              <?php echo $success_message; ?>
          </div>
      <?php endif; ?>
      <!-- Hidden input for the Create action -->
      <form method="POST" action="index.php" id="create-list-form" class="create-list-form">
        <input type="hidden" name="action" value="create_list">
        <div class="input-group">
          <label for="list-name">Create a new list</label>
          <input type="text" id="list-name" name="list_name" placeholder="Enter new list name" required value="<?php echo ($error_message && isset($_POST['list_name'])) ? htmlspecialchars($_POST['list_name']) : ''; ?>">
        </div>
        <button type="submit" class="btn-primary">Create List</button>
      </form>
    </section>

    <!----------------------DYNAMIC LISTS SECTION---------------------->
    <div class="lists-wrapper">
      
      <!-- Pinned Section -->
      <section class="lists-section" id="pinned-section">
        <div class="lists-header">
          <div class="lists-title-area">
            <h2>Pinned</h2>
          </div>
          <div class="menu-container pinned-section-menu">
             <button type="button" class="menu-btn">
               <img src="images/dots.svg" alt="Options">
             </button>
          </div>
        </div>
        
        <div class="lists-grid" id="pinned-container">
          <!-- Card 1 (Pinned) -->
          <div class="list-card">
            <div class="card-header">
              <div class="card-title">
                <h3>MULTIMEDIA</h3>
                <span>1 of 3 done</span>
              </div>
              <div class="menu-container">
                <button type="button" class="menu-btn">
                  <img src="images/dots.svg" alt="Options">
                </button>
                <div class="dropdown-menu">
                  <!-- Pinned Card Action Forms -->
                  <form method="POST" action="index.php" style="margin: 0; padding: 0;">
                    <input type="hidden" name="action" value="unpin_list">
                    <input type="hidden" name="list_id" value="placeholder_id_1">
                    <button type="submit" class="dropdown-item pin-list" style="width: 100%;"><img src="images/unpin.svg" alt="Unpin" class="icon-sm"> Unpin</button>
                  </form>
                  <button type="button" class="dropdown-item edit-list" style="width: 100%;"><img src="images/edit.svg" alt="Edit" class="icon-sm"> Edit</button>
                  <form method="POST" action="index.php" style="margin: 0; padding: 0;">
                    <input type="hidden" name="action" value="delete_list">
                    <input type="hidden" name="list_id" value="placeholder_id_1">
                    <button type="submit" class="dropdown-item delete-list" style="width: 100%;"><img src="images/delete.svg" alt="Delete" class="icon-sm"> Delete</button>
                  </form>
                </div>
              </div>
            </div>
            <ul class="task-items">
              <li class="task-item">
                <label class="custom-checkbox">
                  <input type="checkbox" checked>
                  <span class="checkmark"></span>
                  <span class="task-text">Group Activity</span>
                </label>
              </li>
              <li class="task-item">
                <label class="custom-checkbox">
                  <input type="checkbox">
                  <span class="checkmark"></span>
                  <span class="task-text">Lessons Review</span>
                </label>
              </li>
              <li class="task-item">
                <label class="custom-checkbox">
                  <input type="checkbox">
                  <span class="checkmark"></span>
                  <span class="task-text">Final Activity</span>
                </label>
              </li>
            </ul>
          </div>
          
        </div>
      </section>

      <!-- Regular Lists Section -->
      <section class="lists-section my-lists-section">
        <div class="lists-header">
          <div class="lists-title-area">
            <h2>My Lists</h2>
          </div>
          <div class="lists-count" id="list-count-badge">6 Lists</div>
        </div>

        <!-- Empty State Container -->
        <div id="empty-state" class="empty-state hidden">
          <div class="empty-icon">+</div>
          <h3 class="empty-title">No Existing List</h3>
        </div>

        <div class="lists-grid" id="my-lists-container">
          
          <!-- Card 2 (My Lists) -->
          <div class="list-card">
            <div class="card-header">
              <div class="card-title"><h3>COMP 018</h3><span>1 of 3 done</span></div>
              <div class="menu-container">
                <button type="button" class="menu-btn"><img src="images/dots.svg" alt="Options"></button>
                <div class="dropdown-menu">
                  <!-- Regular Card Action Forms -->
                  <form method="POST" action="index.php" style="margin: 0; padding: 0;">
                    <input type="hidden" name="action" value="pin_list">
                    <input type="hidden" name="list_id" value="placeholder_id_7">
                    <button type="submit" class="dropdown-item pin-list" style="width: 100%;"><img src="images/pin.svg" alt="Pin" class="icon-sm"> Pin</button>
                  </form>
                  <button type="button" class="dropdown-item edit-list" style="width: 100%;"><img src="images/edit.svg" alt="Edit" class="icon-sm"> Edit</button>
                  <form method="POST" action="index.php" style="margin: 0; padding: 0;">
                    <input type="hidden" name="action" value="delete_list">
                    <input type="hidden" name="list_id" value="placeholder_id_7">
                    <button type="submit" class="dropdown-item delete-list" style="width: 100%;"><img src="images/delete.svg" alt="Delete" class="icon-sm"> Delete</button>
                  </form>
                </div>
              </div>
            </div>
            <ul class="task-items">
              <li class="task-item"><label class="custom-checkbox"><input type="checkbox" checked><span class="checkmark"></span><span class="task-text">Narrative Report</span></label></li>
              <li class="task-item"><label class="custom-checkbox"><input type="checkbox"><span class="checkmark"></span><span class="task-text">Database ERD</span></label></li>
              <li class="task-item"><label class="custom-checkbox"><input type="checkbox"><span class="checkmark"></span><span class="task-text">Code Review</span></label></li>
            </ul>
          </div>

        </div>
      </section>
    </div>
  </main>

  <!----------------------EDIT STATE MODAL---------------------->
  <div class="modal-overlay" id="edit-modal">
    <div class="edit-list-modal">
      <!-- Edit and Update Content Form (Wrapped around header to submit list name) -->
      <form method="POST" action="index.php" id="update-modal-form">
        <div class="modal-header">
          <div class="modal-header-left">
            <span class="modal-supertitle">EDIT LIST</span>
            <div class="modal-title-box">
              <input type="text" class="modal-title-input" name="list_name" value="COMP 018" required>
              <span class="modal-progress">1 of 5 done</span>
            </div>
          </div>
          <button class="btn-close" id="close-modal" type="button">X</button>
        </div>

        <!-- Hidden Inputs for Backend -->
        <input type="hidden" name="action" value="update_list">
        <input type="hidden" name="list_id" value="placeholder_modal_id">

        <div class="modal-add-task">
          <label>Add new task</label>
          <div class="add-task-row">
            <input type="text" name="new_task" placeholder="Enter new task">
            <button type="button" class="btn-primary">+ Add</button>
          </div>
        </div>

        <div class="modal-tasks-container">
          <div class="modal-tasks-header">
            <span>Current Tasks: &nbsp;3</span>
          </div>
          
          <ul class="modal-task-list">
            
            <li class="modal-task-item">
              <label class="custom-checkbox">
                <input type="checkbox" name="task_status[]" value="1" checked>
                <span class="checkmark"></span>
              </label>
              <div class="task-content">
                <!-- Array Style Naming Convention of Task -->
                <input type="text" class="task-text-edit" name="tasks[]" value="Narrative Report">
              </div>
              <div class="task-actions">
                <button type="button" class="btn-action delete-task-btn"><img src="images/delete.svg" alt="Delete" class="icon-sm"></button>
              </div>
            </li>

            <li class="modal-task-item">
              <label class="custom-checkbox">
                <input type="checkbox" name="task_status[]" value="1">
                <span class="checkmark"></span>
              </label>
              <div class="task-content">
                <input type="text" class="task-text-edit" name="tasks[]" value="Database ERD">
              </div>
              <div class="task-actions">
                <button type="button" class="btn-action delete-task-btn"><img src="images/delete.svg" alt="Delete" class="icon-sm"></button>
              </div>
            </li>
            
            <li class="modal-task-item">
              <label class="custom-checkbox">
                <input type="checkbox" name="task_status[]" value="1">
                <span class="checkmark"></span>
              </label>
              <div class="task-content">
                <input type="text" class="task-text-edit" name="tasks[]" value="Code Review">
              </div>
              <div class="task-actions">
                <button type="button" class="btn-action delete-task-btn"><img src="images/delete.svg" alt="Delete" class="icon-sm"></button>
              </div>
            </li>

          </ul>
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn-primary btn-update" id="btn-update">Update</button>
        </div>
      </form>
    </div>
  </div>

  <script src="script.js"></script>
</body>
</html>