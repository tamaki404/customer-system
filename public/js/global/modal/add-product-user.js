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


                        
                        function addProduct(id, name, category, unit, weight) {
                            // Prevent duplicates
                            if (document.querySelector(`#selected-products tr[data-id="${id}"]`)) {
                                alert("This product is already added.");
                                resetFilters();
                                return;
                            }

                            // Build row
                            let row = `
                                <tr data-id="${id}">
                                    <td>
                                        ${name}
                                        <input type="hidden" name="products[${id}][product_id]" value="${id}">
                                    </td>
                                    <td>${category}</td>
                                    <td>${unit}</td>
                                    <td>${weight}</td>
                                    <td>
                                        <input type="number" step="0.01" class="form-control"
                                            name="products[${id}][price]" placeholder="Enter price" required>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)">Remove</button>
                                    </td>
                                </tr>
                            `;

                            document.querySelector("#selected-products tbody").insertAdjacentHTML('beforeend', row);

                            // Reset filters after adding
                            resetFilters();
                        }

                        function removeRow(button) {
                            button.closest("tr").remove();
                        }

                        function resetFilters() {
                            document.getElementById('filter-category').value = "";
                            document.getElementById('filter-unit').value = "";
                            document.getElementById('filter-weight').value = "";
                            document.getElementById('product-results').innerHTML = "";
                        }
