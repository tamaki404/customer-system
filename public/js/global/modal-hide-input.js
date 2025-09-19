
                document.addEventListener("DOMContentLoaded", function () {
                    const accStatus = document.getElementById("acc_status");
                    const reasonGroup = document.getElementById("reason_group");
                    const reasonSelect = document.getElementById("reason_to_decline");

                    function toggleReasonField() {
                        if (accStatus.value === "Declined") {
                            reasonGroup.style.display = "block";
                            reasonSelect.setAttribute("required", "required");
                        } else {
                            reasonGroup.style.display = "none";
                            reasonSelect.removeAttribute("required");
                            reasonSelect.value = ""; 
                        }
                    }

                    toggleReasonField();

                    accStatus.addEventListener("change", toggleReasonField);
                });