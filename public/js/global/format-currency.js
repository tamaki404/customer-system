                          document.getElementById("credit_limit").addEventListener("input", function(e) {
                            let value = e.target.value.replace(/,/g, ""); 
                            if (!isNaN(value) && value !== "") {
                                e.target.value = parseFloat(value).toLocaleString("en-US");
                            }
                        });

                        document.querySelector("form").addEventListener("submit", function() {
                            let input = document.getElementById("credit_limit");
                            input.value = input.value.replace(/,/g, ""); 
                        });