import matplotlib.pyplot as plt
import numpy as np

# Data
x = np.array([1, 2, 3])
y = np.array([150, 160, 170])

# Hitung slope dan intercept
m = 10
c = 140

# Garis regresi
x_line = np.linspace(1, 3, 100)
y_line = m * x_line + c

# Plot
plt.scatter(x, y, color='blue', label='Data')
plt.plot(x_line, y_line, color='red', linestyle='--', label=f'y = {m}x + {c}')
plt.xticks([1, 2, 3], ['Februari', 'Maret', 'April'])
plt.xlabel('Bulan')
plt.ylabel('Penggunaan Stok')
plt.title('Regresi Linear Penggunaan Stok per Bulan')
plt.legend()
plt.grid(True)
plt.tight_layout()
plt.show()
